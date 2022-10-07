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
        $tableName = config('lunarphp-virtual-product.code_pool.batches_table');
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->morphs('purchasable');
            $table->integer('entry_price')->unsigned()->nullable();
            $table->bigInteger('entry_price_currency_id')->unsigned();
            $table->text('notes')->nullable();
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
        $tableName = config('lunarphp-virtual-product.code_pool.batches_table');
        Schema::dropIfExists($tableName);
    }
};
