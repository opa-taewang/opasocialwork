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
        Schema::create('seoservices', function (Blueprint $table) {
            $table->id()->primary();
            $table->text('name')->nullable();
            $table->text('slug')->nullable();
            $table->text('description')->nullable();
            $table->integer('category_id', 11)->nullable();
            $table->tinyInteger('status', 1)->default(1);
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('rank')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_services');
    }
};
