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
        Schema::create('hotel_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id');
            $table->string('coupon_code')->unique();
            $table->string('coupon_name');
            $table->enum('type', ['Fixed', 'Percentage']);
            $table->decimal('value', 8, 2);
            $table->string('available')->nullable(); // Once, Limited, NotLimited
            $table->date('expiration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_coupons');
    }
};
