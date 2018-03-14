<?php

namespace App\Http\Controllers;
use Request;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use App\Http\Models\common\Constant;
use App\Http\Models\business\Image;

use Mail;

class ImageController extends Controller
{ 
    private $_image = null;

    public function __construct()
    {
        parent::__construct();
        $this->_image = new Image();
    }

    public function script()
    {
        return view('admin.image.script')->with('title', 'jiaoben');
    }

    public function imagemigration()
    {
        $time = microtime(true);
        $rs = $this->_image->filemigration();
        $runtime = microtime(true) - $time;
        return json_encode($runtime);
    }
}