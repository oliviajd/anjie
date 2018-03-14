<?php

namespace App\Http\Models\common;

class Pdo
{
	/**
     * PDO实例对象
     *
     * @var string
     */
    private $_pdo = null;
    private $dsn = null;
    private $_stmt = null;
    private $times = null;

    public function __construct($database='anjie') 
    {
        try {
            if ($database == 'anjie') {
                $this->dsn = "mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_DATABASE') . ";port=" . env('DB_PORT');
                $this->_pdo = new \PDO($this->dsn, env('DB_USERNAME'), env('DB_PASSWORD'),  array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',\PDO::ATTR_PERSISTENT => true));
            } elseif ($database == 'bankdata') {
                $this->dsn = "mysql:host=" . env('DB_HOST') . ";dbname=" . env('DB_BANK_DATABASE') . ";port=" . env('DB_PORT');
                $this->_pdo = new \PDO($this->dsn, env('DB_BANK_USERNAME'), env('DB_BANK_PASSWORD'),  array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',\PDO::ATTR_PERSISTENT => true));
            }
            $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            $this->outputError($e->getMessage());
        }
    }

    //执行SQL  
    public function execute($_sql, $params=null) 
    { 
        try {  
            $this->_stmt = $this->_pdo->prepare($_sql);
            $this->_times['start_time'] = microtime(1);
            if (is_array($params)) {
                $this->_stmt->execute($params); 
            } else {
                $rs = $this->_stmt->execute();
            }
            $this->_times['end_time'] = microtime(1);
            $data = array();
            $data['time'] = $this->_times['end_time'] - $this->_times['start_time'];
            $data['sql'] = $_sql;
            $str = array();
            $str[] = 'date:' . date('Y-m-d H:i:s');
            $str[] = print_r($data, true);
            $str[] = "\n\n";
            if (env('DB_DEBUG') == 'ON') {
                if (!file_exists(storage_path() . '/mysqllogs')) {
                    mkdir(storage_path() . '/mysqllogs', 0777, true);
                }
                file_put_contents(storage_path() . '/mysqllogs/api_' . date('Y-m-d') . '.log', implode("\n", $str), FILE_APPEND);
            }
        } catch (\PDOException $e) {   
            $this->outputError('SQL语句：'.$_sql.'<br />错误信息：'.$e->getMessage());
        }
        return $this->_stmt;  
    }  

    /**
     * 返回数据列表的二维关联数组
     * 
     * @return array(array{}) | empty array | false
     */
    public function fetchAll($_sql, $params=null, $fetch_argument = \PDO::FETCH_ASSOC)
    {
        try {  
            $this->_stmt = $this->_pdo->prepare($_sql);
            $this->_times['start_time'] = microtime(1);
            if (is_array($params)) {
                $this->_stmt->execute($params); 
            } else {
                $this->_stmt->execute();
            }
            $rs =  $this->_stmt->fetchAll($fetch_argument);
            $this->_times['end_time'] = microtime(1);
            $data = array();
            $data['time'] = $this->_times['end_time'] - $this->_times['start_time'];
            $data['sql'] = $_sql;
            $str = array();
            $str[] = 'date:' . date('Y-m-d H:i:s');
            $str[] = print_r($data, true);
            $str[] = "\n";
            if (env('DB_DEBUG') == 'ON') {
                if (!file_exists(storage_path() . '/mysqllogs')) {
                    mkdir(storage_path() . '/mysqllogs', 0777, true);
                }
                file_put_contents(storage_path() . '/mysqllogs/api_' . date('Y-m-d') . '.log', implode("\n", $str), FILE_APPEND);
            }
            return $rs === false ? array () : $rs;
        } catch (\PDOException $e) {  
            $this->outputError('SQL语句：'.$_sql.'<br />错误信息：'.$e->getMessage());
        }  
        return $this->_stmt;
    }

    /**
     * 返回数据行的一维关联数组
     * 
     * @return array(array{}) | empty array | false
     */
    public function fetchOne($_sql, $params=null, $fetch_style = \PDO::FETCH_ASSOC)
    {
        try {  
            $this->_stmt = $this->_pdo->prepare($_sql);
            $this->_times['start_time'] = microtime(1);
            if (is_array($params)) {
                $this->_stmt->execute($params); 
            } else {
                $this->_stmt->execute();
            }
            $rs = $this->_stmt->fetch($fetch_style);
            $this->_times['end_time'] = microtime(1);
            $data = array();
            $data['time'] = $this->_times['end_time'] - $this->_times['start_time'];
            $data['sql'] = $_sql;
            $str = array();
            $str[] = 'date:' . date('Y-m-d H:i:s');
            $str[] = print_r($data, true);
            $str[] = "\n\n";
            if (env('DB_DEBUG') == 'ON') {
                if (!file_exists(storage_path() . '/mysqllogs')) {
                    mkdir(storage_path() . '/mysqllogs', 0777, true);
                }
                file_put_contents(storage_path() . '/mysqllogs/api_' . date('Y-m-d') . '.log', implode("\n", $str), FILE_APPEND);
            }
            return $rs === false ? array () : $rs;
        } catch (\PDOException $e) {  
            $this->outputError('SQL语句：'.$_sql.'<br />错误信息：'.$e->getMessage());
        }  
        return $this->_stmt;
    }

     /**
     * beginTransaction 事务开始
     */
    public function beginTransaction()
    {
        $this->_pdo->beginTransaction();
    }

     /**
     * commit 事务提交
     */
    public function commit()
    {
        $this->_pdo->commit();
    }

     /**
     * rollback 事务回滚
     */
    public function rollback()
    {
        $this->_pdo->rollback();
    }

    /**
     * 输出错误信息
     *
     * @param String $strErrMsg
     */
    public function outputError($strErrMsg)
    {
        throw new \Exception('MySQL Error: '.$strErrMsg);
    }

    /**
     * destruct 关闭数据库连接
     */
    public function destruct()
    {
        $this->_pdo = null;
    }
    /**
     * 返回最后插入行的ID或序列值
     */
    public function lastInsertId()
    {
        return $this->_pdo->lastInsertId();
    }

    public function RowsAffected()
    {
        return $this->_stmt->rowCount();
    }

    public function pdoClose()
    {
        $this->dsn = null;
    }
}