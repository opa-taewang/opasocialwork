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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('invoice_id', 255);
            $table->string('email', 255);
            $table->decimal('amount', 15, 7)->default(0.00000);
            $table->string('status')->default('PENDING');
            $table->integer('user_id', 10)->unsigned();
            $table->tinyInteger('check_count')->unsigned()->default(0);
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
        Schema::dropIfExists('invoices');
    }
};
