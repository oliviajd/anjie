<?php

namespace App\Http\Models\table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\common\Pdo;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class Jcr_csr extends Model
{
    protected $table = 'jcr_csr';
    public $primaryKey = 'id';
    public $timestamps = false;

    protected $_pdo = null;

    public function __construct()
    {
        parent::__construct();
        $this->_pdo = new Pdo();
    }

    //通过条件查询
    public function getdetail($where, $order = '', $limit = '')
    {
        $sql = "select * from jcr_csr  " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());

        if (!empty($rs)){
            foreach ($rs as $k =>$v){
                $detail = Jcr_csr_car::getDetailById($v['history_id']);
                if($detail){
                    $rs[$k]['reg_date'] = $detail['reg_date'];
                    $rs[$k]['mile'] = $detail['mile'];
                    $rs[$k]['series_name'] = $detail['series_name'];
                    $rs[$k]['zone_name'] = $detail['zone_name'];
                }
            }
        }
        return $rs;
    }

    //通过条件查询
    public function getcount($where)
    {
        $sql = "select count(1) as count from jcr_csr  " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    //通过条件查询
    public function getdetail2($where, $order = '', $limit = '')
    {
        $sql = "SELECT a.user_id, b.id, a.name, a.shopname,a.card_No, a.id as verify_id,a.loginname, a.area, a.address, b.create_time, b.csrrequestverfiy_time, b.car_stock, b.money, b.deadline, b.rate, b.money_request, b.rate_request, b.csr_no, b.car_type, b.car_model, b.crsrequest_status FROM `jcr_verify` as a, jcr_csr as b " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    //通过条件查询
    public function getcount2($where)
    {
        $sql = "select count(1) as count FROM `jcr_verify` as a, jcr_csr as b  " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    //通过条件查询
    public function getdetail3($where, $order = '', $limit = '')
    {
        $sql = "SELECT a.* FROM `jcr_csr` as a LEFT JOIN jcr_bill as b  ON a.id = b.csr_id " . $where . $order . $limit;
        $rs = $this->_pdo->fetchAll($sql, array());
        if (!empty($rs)){
            foreach ($rs as $k =>$v){
                $detail = Jcr_csr_car::getDetailById($v['history_id']);
                if($detail){
                    $rs[$k]['reg_date'] = $detail['reg_date'];
                    $rs[$k]['mile'] = $detail['mile'];
                    $rs[$k]['series_name'] = $detail['series_name'];
                    $rs[$k]['zone_name'] = $detail['zone_name'];
                }
            }
        }

        return $rs;
    }

    //通过条件查询
    public function getcount3($where)
    {
        $sql = "select count(1) as count FROM `jcr_csr` as a LEFT JOIN jcr_bill as b  ON a.id = b.csr_id " . $where;
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    public function getinfobycsrid($csrid)
    {
        $sql = "select * from jcr_csr where id = ?";
        $rs = $this->_pdo->fetchOne($sql, array($csrid));
        return $rs;
    }

    //融资申请
    public function csrrequest($params, $user_id)
    {
        $sql = "insert into jcr_csr (user_id, money_request, deadline_request, rate_request, csr_no,car_model,history_id,car_type, create_time, modify_time) values (?, ?, ?, ?, ?, ?, ?, 2, ?, ?)";
        $rs = $this->_pdo->execute($sql, array($user_id, $params['money'], $params['deadline'], $params['rate'], $params['csr_no'], $params['car_model'], $params['history_id'], time(), time()));
        $return = $params;
        $return['id'] = $this->_pdo->lastInsertId();
        $result = $this->getinfobycsrid($return['id']);
        return $result;
    }

    //融资申请审核
    public function csrrequestverify($params, $user_id)
    {
        $sql = "update jcr_csr set money=?, deadline =?, rate=?, modify_time =?, csrrequestverfiy_time = ?, crsrequest_status =? where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['money'], $params['deadline'], $params['rate'], time(), time(), $params['csrrequest_result'], $params['csr_id']));
        $result = $this->getinfobycsrid($params['csr_id']);
        return $result;
    }

    //融资申请审核
    public function csrrequestverify2($params, $user_id)
    {
        $sql = "update jcr_csr set money=?, car_stock=?, deadline =?, rate=?, modify_time =?, csrrequestverfiy_time = ?, crsrequest_status =?, foundtime = ?, main_business =?, credit_score=?, manage_score=?, assets_score=?, debt_score=?, conducive_score=?, has_score =? where id = ?";
        $rs = $this->_pdo->execute($sql, array($params['money'], $params['car_stock'], $params['deadline'], $params['rate'], time(), time(), $params['csrrequest_result'], $params['foundtime'], $params['main_business'], $params['credit_score'], $params['manage_score'], $params['assets_score'], $params['debt_score'], $params['conducive_score'], '1', $params['csr_id']));
        $result = $this->getinfobycsrid($params['csr_id']);
        return $result;
    }

    //拒件
    public function refusework($params)
    {
        $sql = "update jcr_csr set item_status = 3,crsrequest_status = 2, modify_time=?,csrrequestverfiy_time=?, remark=? where id = ?";
        $rs = $this->_pdo->execute($sql, array(time(),time(), $params['remark'], $params['csr_id']));
        return $rs;
    }

    //完成
    public function completework($params)
    {
        $sql = "update jcr_csr set item_status = 2, modify_time=? where id = ?";
        $rs = $this->_pdo->execute($sql, array(time(), $params['csr_id']));
        return $rs;
    }

    public function getbysql($sql)
    {
        $rs = $this->_pdo->fetchAll($sql, array());
        return $rs;
    }

    public function getonebysql($sql)
    {
        $rs = $this->_pdo->fetchOne($sql, array());
        return $rs;
    }

    public function updatehasbill($csr_id, $has_bill)
    {
        $sql = "update jcr_csr set has_bill = ?, billrequest_time =? where id = ?";
        $rs = $this->_pdo->execute($sql, array($has_bill, time(), $csr_id));
        return $rs;
    }

    public function updateloan($has_loan, $csr_id)
    {
        $sql = "update jcr_csr set has_loan = ? where id = ?";
        $rs = $this->_pdo->execute($sql, array($has_loan, $csr_id));
        return $rs;
    }

    public function updateFinancingSingle($user_id)
    {
        $where = " where user_id = " . $user_id . " and item_status = 4";

        $result = $this->getdetail($where);

        if ($result){
            foreach ($result as $key => $value){
                self::where('id',$value['id'])->update(['item_status'=> 1]);
            }
        }

        return $result;
    }

    public function createCsr($data)
    {
        $time = time();
        $data['create_time'] = $time;
        $data['modify_time'] = $time;
        $id = self::insertGetId($data);
        return $id;
    }

    public static function getCarCsrList($p, $n, $param, $fields = ['jcr_csr.*','jcr_verify.shopname','jcr_verify.name','jcr_verify.area','jcr_verify.loginname as mobile'])
    {
        $searchCondition[] = ['jcr_verify.verify_status',1];
        $searchCondition[] = ['jcr_csr.crsrequest_status','>',0];

        foreach ($param as $k => $value) {
            if ($value !== 0 && $value !== '' && $value !== null && $value !== []) {
                switch ($k) {
                    case 'car_model':
                        $searchCondition[] = ['jcr_csr.car_model', 'like', '%' . $param['car_model'] . '%'];
                        break;
                    case 'end_time':
                        $searchCondition[] = ['jcr_csr.create_time', '<=', $value];
                        break;
                    case 'start_time':
                        $searchCondition[] = ['jcr_csr.create_time', '>=', $value];
                        break;
                    case 'status':
                        if ($value == 1) {
                            $searchCondition[] = ['jcr_csr.crsrequest_status', 1];
                        } else {
                            $searchCondition[] = ['jcr_csr.crsrequest_status', 2];
                        }
                        break;
                    default:
                        $searchCondition[] = ['jcr_csr.'.$k, $value];
                        break;
                }

            }
        }

        $result = self::where($searchCondition)
            ->leftJoin('jcr_verify', 'jcr_verify.user_id', '=', 'jcr_csr.user_id');

        $data = $result->paginate($n, $fields, 'p', $p);

        $rs = self::getPageData($data);

        return $rs;
    }

    protected static function getPageData($data)
    {
        if ($data instanceof LengthAwarePaginator) {
            $data = $data->toArray();
        } else {
            throw new Exception('非法的分页数据@500', 9004);
        }

        $rs['rows'] = $data['data'];
        $rs['total'] = $data['total'];

        return $rs;
    }
}
