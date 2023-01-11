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
        Schema::create('fast_replies', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('title', 255)->nullable();
            $table->longText('metextssage')->nullable();
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
        Schema::dropIfExists('fast_replies');
    }
};
