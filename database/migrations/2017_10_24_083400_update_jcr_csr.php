<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateJcrCsr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jcr_csr', function (Blueprint $table) {
            //
            $table->string('remark', 255)->comment('备注')->nullable();
            $table->string('car_model', 255)->comment('车辆型号')->nullable();
            $table->tinyInteger('car_type')->comment('车辆类型,1:新车 2：二手车')->nullable();
            $table->integer('history_id')->comment('历史id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jcr_csr', function (Blueprint $table) {
            //回滚字段
            $table->dropColumn(['remark', 'history_id']);
        });
    }
}
