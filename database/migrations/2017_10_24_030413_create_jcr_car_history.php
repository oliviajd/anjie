<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJcrCarHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $tableName ='jcr_car_history';

        Schema::create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户id');
            $table->integer('model_id')->comment('车型id')->nullable();
            $table->string('reg_date')->comment('注册时间')->nullable();
            $table->string('mile')->comment('公里数（万里）')->nullable();
            $table->string('vin')->comment('车架号')->nullable();
            $table->integer('zone')->comment('城市码')->nullable();
            $table->string('zone_name',100)->comment('城市名称')->nullable();
            $table->string('model_name',100)->comment('车型名称')->nullable();
            $table->string('series_name',100)->comment('车系名称')->nullable();
            $table->string('brand_name',100)->comment('品牌名称')->nullable();
            $table->text('result')->comment('车300结果');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE $tableName comment '车辆历史'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('jcr_car_history');
    }
}
