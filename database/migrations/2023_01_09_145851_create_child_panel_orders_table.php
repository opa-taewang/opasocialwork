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
        Schema::create('child_panel_orders', function (Blueprint $table) {
            $table->id()->primary();
            $table->integer('user_id', '11')->nullable();
            $table->string('domain', 255)->nullable();
            $table->text('admin_user')->nullable();
            $table->text('admin_password')->nullable();
            $table->string('buyer', 255)->nullable();
            $table->string('amount', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->tinyInteger('renew', 1)->default(0);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('expiry_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('child_panel_orders');
    }
};
