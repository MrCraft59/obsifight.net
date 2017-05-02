<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->increments('id');
      $table->string('username', 16)->unique();
      $table->string('email', 50)->unique();
      $table->string('password', 50);
      $table->integer('role')->default(1);
      $table->integer('vote')->default(0);
      $table->float('money')->default(0);
      $table->ipAddress('ip');
      $table->boolean('skin')->default(0);
      $table->boolean('cape')->default(0);
      $table->rememberToken();
      $table->timestamps();
    });

    // Login security
    Schema::create('users_login_retries', function (Blueprint $table) {
      $table->increments('id');
      $table->ipAddress('ip');
      $table->integer('count');
      $table->timestamps();
    });

    // TwoFactorAuth
    Schema::create('users_two_factor_auth_secrets', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('user_id');
      //$table->foreign('user_id')->references('id')->on('users');
      $table->string('secret', 20);
      $table->boolean('enabled');
    });

    // Log
    Schema::create('users_connection_logs', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('user_id');
      //$table->foreign('user_id')->references('id')->on('users');
      $table->ipAddress('ip');
      $table->timestamps();
    });

    // Tokens (lost password / confirmation mail)
    Schema::create('users_tokens', function (Blueprint $table) {
      $table->increments('id');
      $table->string('type', 10);
      $table->integer('user_id');
      //$table->foreign('user_id')->references('id')->on('users');
      $table->uuid('token');
      $table->ipAddress('used_ip')->nullable()->default(null);
      $table->timestamps();
    });

    // ObsiGuard
    Schema::create('users_obsiguard_ips', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('user_id');
      //$table->foreign('user_id')->references('id')->on('users');
      $table->ipAddress('ip');
      $table->timestamps();
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
    Schema::dropIfExists('users_login_retries');
    Schema::dropIfExists('users_two_factor_auth_secrets');
    Schema::dropIfExists('users_connection_logs');
    Schema::dropIfExists('users_tokens');
    Schema::dropIfExists('users_obsiguard_ips');
  }
}