<?php

use Illuminate\Database\Seeder;

class JcdAnjiePrivilegeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('anjie_privilege')->insert([[
            'requestname' => '资金明细',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcd/listfunddetail',
            'not_check' => '2',
            'type' => '1',
        ],[
            'requestname' => '融资申请记录列表',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcd/cardealerrequestrecord',
            'not_check' => '2',
            'type' => '1',
        ],[
            'requestname' => '修改增信接口',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcd/updatecredit',
            'not_check' => '2',
            'type' => '1',
        ],[
            'requestname' => '车商的估价列表',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcd/carevaluaterecord',
            'not_check' => '2',
            'type' => '1',
        ],[
            'requestname' => '车商详情',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcd/getdealerinfo',
            'not_check' => '2',
            'type' => '1',
        ],[
            'requestname' => '修改车商资料',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcd/updateimage',
            'not_check' => '2',
            'type' => '1',
        ]]);
    }
}
