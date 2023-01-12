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
        Schema::create('api_request_params', function (Blueprint $table) {
            $table->id();
            $table->string('param_key', 255);
            $table->string('param_value', 255);
            $table->string('param_type', 255);
            $table->string('api_type', 255);
            $table->bigInteger('api_id')->lenght(10)->unsigned();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('api_id')->references('id')->on('apis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_request_params');
    }
};
