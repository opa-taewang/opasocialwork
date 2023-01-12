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
                    Schema::create('services', function (Blueprint $table) {
                        $table->id();
                        $table->integer('position')->lenght(10)->unsigned();
                        $table->integer('sequence')->lenght(10)->nullable();
                        $table->string('name', 255);
                        $table->string('slug', 255);
                        $table->text('description')->nullable();
                        $table->tinyInteger('top')->lenght(1)->default(0);
                        $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
                        $table->tinyInteger('is_subscription_allowed')->lenght(1)->default(0);
                        $table->string('servicetype', 255)->default('DEFAULT');
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
                    Schema::dropIfExists('services');
                }
            };
