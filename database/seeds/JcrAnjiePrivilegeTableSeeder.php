<?php

use Illuminate\Database\Seeder;

class JcrAnjiePrivilegeTableSeeder extends Seeder
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
            'requestname' => '登录注册接口',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcr/reglogin',
            'not_check' => '1',
            'type' => '2',
        ],[
            'requestname' => '估价历史详情',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcr/gethistorydetail',
            'not_check' => '1',
            'type' => '2',
        ],[
            'requestname' => '估价历史列表',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcr/gethistorylist',
            'not_check' => '1',
            'type' => '2',
        ],[
            'requestname' => '获取总融资人数',
            'method_id' => null,
            'status' => '1',
            'create_time' => time(),
            'modify_time' => time(),
            'path' => '/Jcr/jcr/gettotalfinancingnumber',
            'not_check' => '1',
            'type' => '2',
        ]]);

    }
}
