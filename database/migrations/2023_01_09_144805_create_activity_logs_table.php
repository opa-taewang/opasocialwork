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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name', '255')->nullable();;
            $table->string('description', '255');
            $table->bigInteger('subject_id', '20')->nullable();;
            $table->string('subject_type', '255')->nullable();;
            $table->bigInteger('causer_id', '20')->nullable();;
            $table->string('causer_type', '255')->nullable();;
            $table->text('properties');
            $table->timestamp('created_at')->nullable();;
            $table->timestamp('updated_at')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
