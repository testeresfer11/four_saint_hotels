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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sabee_id')->nullable(); // ID from SabeeApp
            $table->unsignedBigInteger('user_id')->nullable(); // Reference to users table
            $table->string('room_type')->nullable();
            $table->string('rate_plan')->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->integer('guests')->default(1);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->string('payment_status')->default('pending'); // paid, pending, failed
            $table->string('status')->default('pending'); // confirmed, canceled, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
