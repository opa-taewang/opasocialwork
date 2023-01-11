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
                    Schema::create('api_mappings', function (Blueprint $table) {
                        $table->id()->primary();
                        $table->integer('package_id', 10)->unsigned();
                        $table->string('api_package_id', 255);
                        $table->integer('api_id', 10)->unsigned();
                        $table->timestamp('created_at');
                        $table->timestamp('updated_at');

                        $table->foreign('api_id')->references('id')->on('apis');
                    });
                }

                /**
                 * Reverse the migrations.
                 *
                 * @return void
                 */
                public function down()
                {
                    Schema::dropIfExists('api_mappings');
                }
            };
