<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateToBillFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_file', function (Blueprint $table) {
            //
            $table->string('csr_id',40)->comment('jcr_csr中的id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_file', function (Blueprint $table) {
            //
            $table->dropColumn(['csr_id']);
        });
    }
}
