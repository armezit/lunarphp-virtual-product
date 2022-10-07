<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('lunarphp-virtual-product.code_pool.archive_table');
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->bigInteger('batch_id')->unsigned()->index();
            $table->bigInteger('order_line_id')->unsigned()->nullable()->index();
            $table->json('data');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = config('lunarphp-virtual-product.code_pool.archive_table');
        Schema::dropIfExists($tableName);
    }
};
