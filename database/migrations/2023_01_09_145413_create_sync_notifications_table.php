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
        Schema::create('sync_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('api_id')->lenght(10)->unsigned();
            $table->string('api_name', 255);
            $table->integer('package_id')->lenght(10)->unsigned();
            $table->string('package_name', 255);
            $table->string('reason', 255)->default('');
            $table->string('color')->lenght(10)->default('');
            $table->string('action', 255)->default('');
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
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
        Schema::dropIfExists('sync_notifications');
    }
};
