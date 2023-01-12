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
                        $table->id();
                        $table->enum('status', ['SUBMITTED', 'INPROGRESS', 'PARTIAL', 'CANCELLED', 'COMPLETED'])->default('SUBMITTED');
                        $table->string('username', 300)->nullable();
                        $table->integer('min')->lenght(10)->unsigned()->nullable();
                        $table->integer('max')->lenght(10)->unsigned()->nullable();
                        $table->integer('posts')->lenght(10)->unsigned()->nullable();
                        $table->decimal('run_price', 15, 7)->nullable();
                        $table->integer('runs_triggered')->lenght(10)->unsigned()->nullable();
                        $table->integer('user_id')->lenght(10)->unsigned()->nullable();
                        $table->integer('package_id')->lenght(10)->unsigned()->nullable();
                        $table->tinyInteger('dripfeed')->lenght(10)->default(0);
                        $table->integer('dripfeed_runs')->lenght(10)->unsigned()->nullable();
                        $table->integer('dripfeed_interval')->lenght(10)->unsigned()->nullable();
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
