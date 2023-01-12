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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->lenght(10)->unsigned();
            $table->bigInteger('user_id')->lenght(10)->unsigned();
            $table->bigInteger('package_id')->lenght(10)->unsigned();
            $table->integer('posts')->lenght(10)->unsigned();
            $table->decimal('price', 15, 7)->nullable();
            $table->string('link', 500);
            $table->enum('status', ['PENDING', 'ACTIVE', 'COMPLETED', 'STOPPED', 'CANCELLED'])->default('PENDING');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('package_id')->references('id')->on('packages');
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
        Schema::dropIfExists('subscriptions');
    }
};
