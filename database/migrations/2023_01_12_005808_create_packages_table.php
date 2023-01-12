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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->integer('position')->lenght(10)->unsigned();
            $table->integer('sequence')->lenght(10)->unsigned()->nullable();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->decimal('price_per_item', 15, 7);
            $table->decimal('cost_per_item', 15, 7)->default(0.0000000);
            $table->decimal('seller_cost', 15, 7)->default(0.0000000);
            $table->integer('minimum_quantity')->lenght(10)->unsigned();
            $table->integer('maximum_quantity')->lenght(10)->unsigned();
            $table->string('performance', 255)->default('Not Enough Data');
            $table->text('description');
            $table->enum('status', ['ACTIVE', 'INACTIVE']);
            $table->bigInteger('service_id')->lenght(10)->unsigned();
            $table->integer('preferred_api_id')->lenght(10)->unsigned()->nullable();
            $table->tinyInteger('custom_comments')->lenght(1)->default(0);
            $table->tinyInteger('refillbtn')->lenght(10)->default(0);
            $table->enum('features', ['No', 'Drip Feed', 'Auto Like', 'Auto View'])->default('No');
            $table->tinyInteger('top')->lenght(1)->default(0);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->text('license_codes')->nullable();
            $table->text('script')->nullable();
            $table->string('script_name', 255)->nullable();
            $table->string('packagetype', 255)->default('default');
            $table->timestamp('mydate')->useCurrent()->useCurrentOnUpdate();
            // $table->text('order_limit')->default(0);
            $table->text('order_limit');
            $table->integer('position_id')->lenght(10)->default(0);
            $table->integer('refill_period')->lenght(10)->default(30);
            $table->integer('refill_time')->lenght(10)->default(5);

            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
};
