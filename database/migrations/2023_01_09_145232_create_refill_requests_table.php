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
        Schema::create('refill_requests', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('order_id', 191);
            $table->enum('status', ['PENDING', 'IN PROGRESS', 'COMPLETED', 'CANCELLED'])->default('PENDING');
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
        Schema::dropIfExists('refill_requests');
    }
};
