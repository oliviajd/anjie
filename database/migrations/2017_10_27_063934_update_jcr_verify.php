<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateJcrVerify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jcr_verify', function (Blueprint $table) {
            //
            $table->string('validity_time',40)->comment('有效性')->nullable();
            $table->float('quota',8,3)->comment('额度 ')->nullable();
            $table->float('use_quota',8,3)->comment('使用额度 ')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jcr_verify', function (Blueprint $table) {
            //
            $table->dropColumn(['validity_time']);
        });
    }
}
