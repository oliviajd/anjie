<?php

namespace App\Http\Models\common;

use Request;
use App\Http\Models\common\Constant;
use App\Http\Models\common\Pdo;

define('WORK_API_URL', env('WORK_API_URL'));
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 流程实例
 *
 * @author win7
 */
class ProcessInstance 
{

    /**
     * 创建一个实例
     * @param int $process_id <p>
     * 流程ID
     * </p>
     * @return int 若成功则返回实例ID，否则返回false.
     * 
     */
    public function create($user_id, $process_id) 
    {
        $param = array(
            'process_id' => $process_id,
            'user_id' => $user_id,
        );
        $r = curl_post(WORK_API_URL . 'process.instance.create', $param);
        return $r;
    }

    /**
     * 开始一个实例
     * @param int $process_instance_id <p>
     * 流程实例ID
     * </p>
     * @return boolean 若成功则返回任务编号，否则返回false.
     * 
     */
    public function start($user_id, $process_instance_id, $data=array()) 
    {
        $param = array(
            'process_instance_id' => $process_instance_id,
            'user_id' => $user_id,
            'data' => $data,
        );
        $r = curl_post(WORK_API_URL . 'process.instance.start', $param);
        return $r;
    }

    /**
     * 开始一个实例
     * @param int $process_instance_id <p>
     * 流程实例ID
     * </p>
     * @return boolean 若成功则返回任务编号，否则返回false.
     * 
     */
    public function end($process_instance_id) 
    {
        $param = array(
            'process_instance_id' => $process_instance_id,
        );
        $r = curl_post(WORK_API_URL . 'process/stop', $param);
        return $r;
    }

}

/**
 * 节点实例
 *
 * @author win7
 */
class TaskInstance 
{

    /**
     * 任务池
     * @param array $condition <p>
     * role_id 角色ID
     * </p>
     * @return array 返回任务列表，没有任务则返回array().
     * 
     */
    public function lists($condition, $page, $size, $params, $process_id) 
    {
        $param = array(
            'user_id' => $condition['user_id'],
            'role_id' => $condition['role_id'],
            'has_locked' => $condition['has_locked'],
            'has_completed' => $condition['has_completed'],
            'has_stoped' => $condition['has_stoped'],
            'page' => $page,
            'size' => $size,
            'fields' => 'work_id,credit_user_id',
            'condition' => $params,
            'process_id' => $process_id,
            'order'=>isset($condition['order']) ? $condition['order'] : 'create_time',
        );
        $r = curl_post(WORK_API_URL . 'user.task.lists', $param);
        return $r;
    }
    /**
     * 任务池
     * @param array $condition <p>
     * role_id 角色ID
     * </p>
     * @return array 返回任务列表，没有任务则返回array().
     * 
     */
    public function listsjcr($condition, $page, $size, $params, $process_id) 
    {
        $param = array(
            'user_id' => $condition['user_id'],
            'role_id' => $condition['role_id'],
            'has_locked' => $condition['has_locked'],
            'has_completed' => $condition['has_completed'],
            'has_stoped' => $condition['has_stoped'],
            'page' => $page,
            'size' => $size,
            'fields' => 'csr_id',
            'condition' => $params,
            'process_id' => $process_id,
        );
        $r = curl_post(WORK_API_URL . 'user.task.lists', $param);
        return $r;
    }

}

class UserTask extends TaskInstance 
{

    /**
     * 拾取一个任务,拾取后其他人不可再拾取或者追回
     * @param int $task_instance_id <p>
     * 任务实例ID
     * </p>
     * @return boolean 若成功则返回true，否则返回false.
     * 
     */
    public function pickup($user_id, $task_instance_id) 
    {
        $param = array(
            'user_id' => $user_id,
            'task_instance_id' => $task_instance_id,
        );
        $r = curl_post(WORK_API_URL . 'user.task.pickup', $param);
        return $r;
    }

    /**
     * 丢弃一个任务,丢弃后其他人可再拾取或者追回
     * @param int $task_instance_id <p>
     * 任务实例ID
     * </p>
     * @return boolean 若成功则返回true，否则返回false.
     * 
     */
    public function giveup($user_id, $task_instance_id) 
    {
        $param = array(
            'user_id' => $user_id,
            'task_instance_id' => $task_instance_id,
        );
        $r = curl_post(WORK_API_URL . 'user.task.giveup', $param);
        return $r;
    }
    /**
     * 修改申请件的内容
     * @param int $process_instance_id <p>
     * 任务实例ID
     * </p>
     * @return boolean 若成功则返回true，否则返回false.
     * 
     */
    public function edit($process_instance_id, $process_id, $data) 
    {
        $param = array(
            'process_instance_id' => $process_instance_id,
            'process_id' => $process_id,
            'data' => json_encode($data),
        );
        $r = curl_post(WORK_API_URL . 'user.task.edit', $param);
        return $r;
    }

    /**
     * 追回一个任务，下个节点的任务未被完成前可以追回
     * @param int $task_instance_id <p>
     * 当前任务实例ID
     * </p>
     * @return boolean 若成功则返回true，否则返回false.
     * 
     */
    public function fetch($user_id, $task_instance_id) 
    {
        
    }

    /**
     * 完成一个任务
     * @param int $task_instance_id <p>
     * 任务实例ID
     * </p>
     * @return boolean 若成功则返回true，否则返回false.
     * 
     */
    public function complete($user_id, $task_instance_id, $data) 
    {
        $param = array(
            'user_id' => $user_id,
            'task_instance_id' => $task_instance_id,
            'data' => $data,//json格式的参数
        );
        $r = curl_post(WORK_API_URL . 'user.task.complete', $param);
        return $r;
    }

}
function curl_post($post_url, $post_data) 
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $post_url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    return $error ? $error : $result;
}
