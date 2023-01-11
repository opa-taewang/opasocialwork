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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('topic', 255)->default('Others');
            $table->string('subject', 255);
            $table->enum('status', ['OPEN', 'PENDING', 'ANSWERED', 'CLOSED'])->default('OPEN');
            $table->text('description');
            $table->integer('user_id', 10)->unsigned();
            $table->tinyInteger('is_read')->default(0);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->string('request', 255)->nullable();
            $table->string('orderids', 255)->nullable();
            $table->string('paymentmode', 255)->nullable();
            $table->string('transaction', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('amount', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
