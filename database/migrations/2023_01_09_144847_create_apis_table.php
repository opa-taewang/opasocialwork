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
        Schema::create('apis', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name', 255);
            $table->decimal('rate', 15, 7)->default(0.0000000);
            $table->text('order_end_point');
            $table->text('order_success_response');
            $table->text('order_method');
            $table->text('status_end_point');
            $table->text('status_success_response');
            $table->text('status_method');
            $table->text('package_end_point');
            $table->text('package_method');
            $table->string('order_id_key', 255);
            $table->string('start_counter_key', 255);
            $table->string('status_key', 255);
            $table->string('remains_key', 255);
            $table->string('package_id_key', 255);
            $table->string('package_name', 255);
            $table->string('rate_key', 255);
            $table->string('min_key', 255);
            $table->string('max_key', 255);
            $table->string('service_key', 255);
            $table->string('type_key', 255);
            $table->string('desc_key', 255);
            $table->tinyInteger('process_all_order', 4)->default(0);
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
        Schema::dropIfExists('apis');
    }
};
