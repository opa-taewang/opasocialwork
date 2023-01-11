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
        Schema::create('downloadrecords', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('script_name', 255)->nullable();
            $table->string('ip', 255)->nullable();
            $table->integer('downloads', 11)->default(0);
            $table->integer('user_id', 11)->nullable();
            $table->integer('orderid', 11)->nullable();
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
        Schema::dropIfExists('download_records');
    }
};
