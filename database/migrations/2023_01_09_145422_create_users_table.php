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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 191);
            $table->string('username', 255);
            $table->decimal('funds', 15, 7)->default('0.00000');
            $table->string('password', 255);
            $table->enum('status', ['ACTIVE', 'DEACTIVATED', 'DELETED'])->default('ACTIVE');
            $table->enum('role', ['ADMIN', 'MODERATOR', 'USER'])->default("USER");
            $table->string('api_token', 191)->nullable();
            $table->string('enabled_payment_methods', 255)->nullable();
            // $table->string('skype_id', 255)->nullanle();
            $table->string('timezone', 255)->default('Africa/Lagos');
            $table->timestamp('last_login')->nullable();
            $table->integer('group_id')->lenght(10)->nullable();
            $table->text('favorite_pkgs')->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->integer('currency_id')->lenght(10)->default(2);
            $table->string('ip', 255)->nullable();
            $table->decimal('reffund', 15, 7)->default(0.00000);
            $table->decimal('treffund', 15, 7)->default(0.00000);
            $table->string('fb_id', 255)->nullable();
            $table->string('g_id', 255)->nullable();
            $table->string('user_from', 255)->nullable();
            $table->float('points')->default(0);
            $table->string('daold', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('verified')->default(0);
            $table->boolean('is_subscribed_newsletter')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
