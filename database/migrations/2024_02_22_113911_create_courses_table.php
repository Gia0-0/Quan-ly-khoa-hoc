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
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id');
            $table->string('course_name');
            $table->string('course_description')->nullable();
            $table->string('slug');
            $table->decimal('price', 18, 0);
            $table->decimal('price_sale', 18, 0)->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_name')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'expert', 'all'])->default('all');
            $table->enum('status', ['available', 'upcoming'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
