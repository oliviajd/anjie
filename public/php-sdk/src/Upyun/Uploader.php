<?php
namespace Upyun;

use Upyun\Api\Rest;
use Upyun\Api\Form;
use GuzzleHttp\Psr7;
use Illuminate\Support\Facades\DB;

class Uploader {
    /**
     * @var Config
     */
    protected $config;

    protected $useBlock = false;


    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function upload($path, $file, $params, $withAsyncProcess) {
        $stream = Psr7\stream_for($file);
        $size = $stream->getSize();
        $useBlock = $this->needUseBlock($size);

        if ($withAsyncProcess) {
            $req = new Form($this->config);
            return $req->upload($path, $stream, $params);
        }

        if(! $useBlock) {
            $req = new Rest($this->config);
            return $req->request('PUT', $path)
                       ->withHeaders($params)
                       ->withFile($stream)
                       ->send();
        } else {
            return $this->pointUpload($path, $stream, $params);
        }
    }

    /**
     *  断点续传
     * @param $path
     * @param $stream
     * @param $params
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function pointUpload($request, $file, $params) {
        $path = $request['path'] . $request['file_id'] . '.' .  $request['suffix'];
        $stream = Psr7\stream_for($file);
        $req = new Rest($this->config);
        $headers = array();
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                $headers['X-Upyun-Meta-' . $key] = $val;
            }
        }
        if ($request['upyun_uploads_offset'] == '0') {
            $res = $req->request('PUT', $path)
                ->withHeaders(array_merge(array(
                    'X-Upyun-Multi-Stage' => 'initiate',
                    'X-Upyun-Multi-Type' => Psr7\mimetype_from_filename($path),
                    'X-Upyun-Multi-Length' => $stream->getSize(),
                ), $headers))
                ->send();
            if ($res->getStatusCode() !== 204) {
                throw new \Exception('init request failed when poinit upload!', -1);
            }

            $init      = Util::getHeaderParams($res->getHeaders());
            $uuid      = $init['x-upyun-multi-uuid'];
        } else {
            $uuid = $request['upyun_uuid'];
        }
        
        $blockSize = 1024 * 1024;
        $partId = $request['upyun_uploads_offset'];
        do {
            $stream->seek($blockSize * $partId);
            $fileBlock = $stream->read($blockSize);
            $res = $req->request('PUT', $path)
                ->withHeaders(array(
                    'X-Upyun-Multi-Stage' => 'upload',
                    'X-Upyun-Multi-Uuid' => $uuid,
                    'X-Upyun-Part-Id' => $partId
                ))
                ->withFile(Psr7\stream_for($fileBlock))
                ->send();

            if ($res->getStatusCode() !== 204) {
                throw new \Exception('upload request failed when poinit upload!', -1);
            }
            $data   = Util::getHeaderParams($res->getHeaders());
            $partId = $data['x-upyun-next-part-id']; 
            $update = DB::statement("update file_upload set upyun_uploads_offset = ?, upyun_uuid = ? where id = ?", [$partId, $uuid, $request['id']]);
        } while ($partId != -1);

        $res = $req->request('PUT', $path)
            ->withHeaders(array(
                'X-Upyun-Multi-Uuid' => $uuid,
                'X-Upyun-Multi-Stage' => 'complete'
            ))
            ->send();

        if ($res->getStatusCode() != 204 && $res->getStatusCode() != 201) {
            throw new \Exception('end request failed when poinit upload!', -1);
        }
        return $res;
    }

    private function needUseBlock($fileSize) {
        if($this->config->uploadType === 'BLOCK') {
            return true;
        } else if($this->config->uploadType === 'AUTO' &&
                  $fileSize >= $this->config->sizeBoundary ) {
            return true;
        } else {
            return false;
        }
    }
}