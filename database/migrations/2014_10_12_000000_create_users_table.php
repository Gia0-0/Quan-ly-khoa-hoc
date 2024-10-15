<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('email')->unique();
            $table->string('password');
            $table->text('active_token')->nullable();
            $table->dateTime('active_expire')->nullable();
            $table->enum('user_type', ['admin', 'teacher', 'student'])->default('student');
            $table->enum('status', ['temporary', 'suspension', 'normal'])->default('suspension');
            $table->dateTime('last_login_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
