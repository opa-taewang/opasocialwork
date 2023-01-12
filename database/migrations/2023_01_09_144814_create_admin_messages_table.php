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
        Schema::create('admin_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id')->lenght(10)->unsigned();
            $table->integer('user_id')->lenght(10)->unsigned();
            $table->string('type')->default('');
            $table->text('title');
            $table->text('message');
            $table->enum('status', ['SENT', 'READ'])->default('SENT');
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
        Schema::dropIfExists('admin_messages');
    }
};
