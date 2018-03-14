<?php

use Illuminate\Database\Seeder;

class JcrCommonValueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('jcr_common_value')->insert([[
            'method_name' => 'gettotalfinancingnumber',
            'key' => 'START_NUM',
            'value' => '2000',
        ],[
            'method_name' => 'gettotalfinancingnumber',
            'key' => 'START_TIME',
            'value' => '2017-11-01',
        ]]);
    }
}
