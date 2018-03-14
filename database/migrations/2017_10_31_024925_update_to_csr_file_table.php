<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateToCsrFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('csr_file', function (Blueprint $table) {
            //
            $table->string('verfiy_id',40)->comment('jcr_verify中的id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('csr_file', function (Blueprint $table) {
            //
            $table->dropColumn(['verfiy_id']);
        });
    }
}
