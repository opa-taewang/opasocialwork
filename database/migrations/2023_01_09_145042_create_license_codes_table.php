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
        Schema::create('licensecodes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 25)->nullable();
            $table->tinyInteger('available')->lenght(1)->nullable();
            $table->string('purchase_by', 255)->nullable();
            $table->timestamp('created_at');
            $table->date('updated_at')->nullable();
            $table->string('package_id', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('license_codes');
    }
};
