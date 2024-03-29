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
                    Schema::create('visits', function (Blueprint $table) {
                        $table->id();
                        $table->integer('refUid')->lenght(10)->unsigned();
                        $table->integer('refVid')->lenght(10)->unsigned();
                        $table->string('visitorIp', 45)->nullable();
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
                    Schema::dropIfExists('visits');
                }
            };
