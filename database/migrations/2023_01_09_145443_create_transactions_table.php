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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigInteger('id', 20);
            $table->decimal('amount', 15, 7)->nullable();
            $table->integer('payment_method_id', 10)->unsigned();
            $table->integer('user_id', 10)->unsigned();
            $table->text('details')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
