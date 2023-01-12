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
                    Schema::create('seopackages', function (Blueprint $table) {
                        $table->id();
                        $table->string('name', 255)->nullable();
                        $table->string('slug', 255)->nullable();
                        $table->text('description')->nullable();
                        $table->double('price')->nullable();
                        $table->tinyInteger('extra')->lenght(1)->default(0);
                        $table->text('extra_content')->nullable();
                        $table->tinyInteger('dripfeed')->lenght(1)->default(0);
                        $table->tinyInteger('custom_field')->lenght(1)->default(0);
                        $table->text('custom_field_name')->nullable();
                        $table->text('custom_field_value')->nullable();
                        $table->integer('service_id')->lenght(11)->nullable();
                        $table->integer('category_id')->lenght(11)->nullable();
                        $table->tinyInteger('status')->lenght(1)->default(1);
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
                    Schema::dropIfExists('seopackages');
                }
            };
