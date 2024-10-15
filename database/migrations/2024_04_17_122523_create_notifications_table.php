<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('type');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('notification_users', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('notification_id')->unsigned();
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_users');
        Schema::dropIfExists('notifications');
    }
};
