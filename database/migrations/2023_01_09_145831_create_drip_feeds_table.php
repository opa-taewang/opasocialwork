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
        Schema::create('drip_feeds', function (Blueprint $table) {
            $table->id()->primary();
            $table->enum('status', ['SUBMITTED', 'INPROGRESS', 'PARTIAL', 'CANCELLED', 'COMPLETED', 'CANCELLING'])->default('SUBMITTED');
            $table->decimal('run_price', 15, 7)->nullable();
            $table->string('link', 300)->nullable();
            $table->integer('run_quantity', 10)->unsigned()->nullable();
            $table->integer('runs', 10)->unsigned()->nullable();
            $table->integer('interval', 10)->unsigned()->nullable();
            $table->integer('runs_triggered', 10)->unsigned()->nullable();
            $table->integer('user_id', 10)->unsigned()->nullable();
            $table->integer('package_id', 10)->unsigned()->nullable();
            $table->integer('active_run_id', 10)->unsigned()->nullable();
            $table->mediumText('cutextstom_comments')->nullable();
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
        Schema::dropIfExists('drip_feeds');
    }
};
