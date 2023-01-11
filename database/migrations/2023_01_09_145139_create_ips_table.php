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
        Schema::create('ips', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('address', 255)->nullable();
            $table->tinyInteger('blocked', 1)->default(0);
            $table->text('reason')->nullable();
            $table->integer('user_id', '11')->nullable();
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
        Schema::dropIfExists('ips');
    }
};
