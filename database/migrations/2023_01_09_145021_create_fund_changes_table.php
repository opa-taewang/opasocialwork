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
        Schema::create('fundchange', function (Blueprint $table) {
            $table->bigInteger('id', 20);
            $table->text('details')->nullable();
            $table->integer('user_id', 10)->unsigned();
            $table->decimal('pricebefore', 15, 7)->nullable();
            $table->decimal('priceafter', 15, 7)->nullable();
            $table->text('reason')->nullable();
            $table->decimal('amount', 15, 7)->nullable();
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
        Schema::dropIfExists('fundchange');
    }
};
