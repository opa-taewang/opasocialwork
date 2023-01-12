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
        Schema::create('seoorders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->lenght(20);
            $table->unsignedBigInteger('package_id')->lenght(20);
            $table->double('total_amount')->nullable();
            $table->tinyInteger('dripfeed')->lenght(1)->default(0);
            $table->longText('retextquirements')->nullable();
            $table->text('extra_services')->nullable();
            $table->string('runs', 255)->nullable();
            $table->string('intervals', 255)->nullable();
            $table->longText('dotextwnloads')->nullable();
            $table->string('status', 255)->nullable();
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
        Schema::dropIfExists('seoorders');
    }
};
