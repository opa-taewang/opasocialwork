->unsigned()<?php

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
                    Schema::create('auto_likes', function (Blueprint $table) {
                        $table->id()->primary();
                        $table->enum('status', ['SUBMITTED', 'INPROGRESS', 'PARTIAL', 'CANCELLED', 'COMPLETED'])->default('SUBMITTED');
                        $table->string('username', 300)->nullable();
                        $table->integer('min', 10)->unsigned()->nullable();
                        $table->integer('max', 10)->unsigned()->nullable();
                        $table->integer('posts', 10)->unsigned()->nullable();
                        $table->decimal('run_price', 15, 7)->nullable();
                        $table->integer('runs_triggered', 10)->unsigned()->nullable();
                        $table->integer('user_id', 10)->unsigned()->nullable();
                        $table->integer('package_id', 10)->unsigned()->nullable();
                        $table->tinyInteger('dripfeed', 4)->default(0);
                        $table->integer('dripfeed_runs', 10)->unsigned()->nullable();
                        $table->integer('dripfeed_interval', 10)->unsigned()->nullable();
                        $table->string('last_post', 255)->nullable();
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
                    Schema::dropIfExists('auto_likes');
                }
            };
