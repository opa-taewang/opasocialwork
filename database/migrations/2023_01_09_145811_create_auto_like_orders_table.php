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
        Schema::create('auto_like_orders', function (Blueprint $table) {
            $table->id()->primary();
            $table->integer('master_id', 10)->unsigned();
            $table->integer('slave_id', 10)->unsigned();
            $table->integer('posttime')->default(0);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_like_orders');
    }
};
