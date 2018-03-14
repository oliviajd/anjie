<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToJcrBillTable extends Migration
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
            $table->string('overdue_days',30)->comment('逾期时间')->nullable();
            $table->string('prepayment_status',2)->comment('1：完成提前还款，2：需要正常还款，3：提前还款中，4：提前还款失败，5：不需要提前还款')->nullable();
            $table->string('prepayment_time',20)->comment('提前还款的时间')->nullable();

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
            $table->dropColumn(['filename','ifcar99_path']);
        });
    }
}
