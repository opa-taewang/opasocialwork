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
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->primary();
            $table->enum('source', ['WEB', 'API'])->default('WEB');
            $table->enum('status', ['COMPLETED', 'PROCESSING', 'INPROGRESS', 'PENDING', 'PARTIAL', 'CANCELLED', 'REFUNDED', 'REFILLING', 'CANCELLING'])->default('PENDING');
            $table->decimal('price', 15, 7)->nullable();
            $table->decimal('cost', 15, 7)->nullable();
            $table->string('link', 500);
            $table->string('start_counter', 255)->nullable();
            $table->string('remains', 255)->nullable();
            $table->integer('quantity', 10)->unsigned();
            $table->integer('user_id', 10)->unsigned();
            $table->integer('package_id', 10)->unsigned();
            $table->integer('api_id', 10)->unsigned()->nullable();
            $table->string('api_order_id', 255)->nullable();
            $table->text('custom_comments')->nullable();
            $table->string('license_code', 255)->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->integer('subscription_id', 10)->nullable();
            $table->string('licenseid', 255)->nullable();
            $table->string('rc', 255)->nullable();

            $table->foreign('package_id')->references('id')->on('packages');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
