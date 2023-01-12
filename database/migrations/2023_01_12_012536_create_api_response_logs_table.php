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
        Schema::create('api_response_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->lenght(10)->unsigned();
            $table->bigInteger('api_id')->lenght(10)->unsigned();
            $table->text('response');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('api_id')->references('id')->on('apis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_response_logs');
    }
};
