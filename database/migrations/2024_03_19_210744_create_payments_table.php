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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('order_id');
            $table->string('partner_code');
            $table->string('request_id');
            $table->string('note')->nullable()->comment('Nội dung thanh toán');
            $table->string('message');
            $table->string('pay_url');
            $table->string('signature')->comment('Chân chữ ký');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
