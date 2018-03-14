<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJcrVerfiyFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('jcr_verfiy_file', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('file_id')->comment('文件id')->nullable();
            $table->integer('verfiy_id')->comment('融资单id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('jcr_verfiy_file');
    }
}
