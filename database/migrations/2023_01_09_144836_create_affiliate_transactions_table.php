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
        Schema::create('affiliate_transactions', function (Blueprint $table) {
            $table->id()->primary();
            $table->integer('package_id', 10)->unsigned();
            $table->integer('refUid', 10)->unsigned();
            $table->integer('buyUid', 10)->unsigned();
            $table->decimal('price', 11, 7);
            $table->decimal('transferedFund', 11, 7);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('buyUid')->references('id')->on('users');
            $table->foreign('package_id')->references('id')->on('packages');
            $table->foreign('refUid')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliate_transactions');
    }
};
