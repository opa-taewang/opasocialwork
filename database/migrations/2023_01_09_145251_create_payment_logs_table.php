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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->bigInteger('id', 20);
            $table->text('details');
            $table->string('currency_code', 255);
            $table->string('total_amount', 255);
            $table->integer('payment_method_id', 10)->unsigned();
            $table->integer('user_id', 10)->unsigned();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->string('amountconversion', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_logs');
    }
};
