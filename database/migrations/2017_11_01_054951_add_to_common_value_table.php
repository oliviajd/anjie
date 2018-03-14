<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToCommonValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName ='jcr_common_value';

        Schema::create($tableName, function (Blueprint $table) {
            //
            $table->increments('id');
            $table->string('method_name')->comment('方法名');
            $table->string('key')->comment('车型id');
            $table->string('value')->comment('值');
        });

        DB::statement("ALTER TABLE $tableName comment '默认值'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jcr_common_value');
    }
}
