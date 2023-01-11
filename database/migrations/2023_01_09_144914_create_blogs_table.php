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
                    Schema::create('blog', function (Blueprint $table) {
                        $table->id()->primary();
                        $table->text('title')->nullable();
                        $table->text('slug')->nullable();
                        $table->text('image')->nullable();
                        $table->text('short_description')->nullable();
                        $table->text('description')->nullable();
                        $table->string('status', 255)->default('Deactivated');
                        $table->integer('pinned')->nullable();
                        $table->dateTime('created_at')->useCurrent()->useCurrentOnUpdate();
                        $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
                        $table->string('views', 255)->nullable();
                    });
                }

                /**
                 * Reverse the migrations.
                 *
                 * @return void
                 */
                public function down()
                {
                    Schema::dropIfExists('blogs');
                }
            };
