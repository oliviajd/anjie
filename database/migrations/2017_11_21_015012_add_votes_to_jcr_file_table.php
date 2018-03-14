<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVotesToJcrFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jcr_file', function (Blueprint $table) {
            //
            $table->string('filename',80)->comment('文件名')->nullable();
            $table->string('ifcar99_path',200)->comment('文件路径')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jcr_file', function (Blueprint $table) {
            //
            $table->dropColumn(['filename','ifcar99_path']);
        });
    }
}
