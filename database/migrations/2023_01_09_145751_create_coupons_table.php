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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable();
            $table->double('min_funds')->nullable();
            $table->integer('max_usage')->lenght(10)->nullable();
            $table->integer('account_age')->lenght(10)->nullable();
            $table->double('amount')->nullable();
            $table->dateTime('expiry')->nullable();
            $table->integer('hours')->lenght(10)->nullable();
            $table->integer('funds')->lenght(10)->nullable();
            $table->string('status', 255)->nullable();
            $table->dateTime('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};
