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
        Schema::create('api_fetch_temps', function (Blueprint $table) {
            $table->id()->primary();
            $table->integer('service_id', 11);
            $table->text('service_name');
            $table->text('api_service_name');
            $table->text('api_package_id');
            $table->text('api_package_name');
            $table->integer('package_id', 11);
            $table->text('package_name');
            $table->text('package_description');
            $table->text('api_package_description');
            $table->text('api_price_per_item');
            $table->decimal('price_per_item', 15, 7);
            $table->integer('api_minimum_quantity', 11);
            $table->integer('minimum_quantity', 11);
            $table->integer('api_maximum_quantity', 11);
            $table->integer('maximum_quantity', 11);
            $table->tinyInteger('type', 4)->default(0);
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
        Schema::dropIfExists('api_fetch_temps');
    }
};
