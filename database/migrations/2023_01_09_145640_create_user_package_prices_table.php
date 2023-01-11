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
        Schema::create('user_package_prices', function (Blueprint $table) {
            $table->id()->primary();
            $table->unsignedBigInteger('user_id', 20);
            $table->unsignedBigInteger('package_id', 20);
            $table->decimal('price_per_item', 15, 7);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_package_prices');
    }
};
