<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToJcrBillTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jcr_bill', function (Blueprint $table) {
            //
            $table->string('service_fee',40)->comment('服务费')->nullable();
            $table->string('loan_time',40)->comment('放款时间')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jcr_bill', function (Blueprint $table) {
            //
            $table->dropColumn(['service_fee','loan_time']);
        });
    }
}
