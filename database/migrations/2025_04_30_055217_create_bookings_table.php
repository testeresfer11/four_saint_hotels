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
            $table->string('reservation_code')->unique();
            $table->unsignedBigInteger('group_id');
            $table->string('door_code')->nullable();
            $table->string('channel_id')->nullable();
            $table->json('license_plate')->nullable();
            $table->foreignId('hotel_id');
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->string('status');
            $table->unsignedBigInteger('room_type_id');
            $table->string('room_type_name');
            $table->unsignedBigInteger('room_id');
            $table->string('room_name');
            $table->unsignedTinyInteger('number_of_guests');
            $table->json(column: 'guest_count');
            $table->decimal('room_price', 10, 2);
            $table->decimal('paid', 10, 2)->default(0);
            $table->string('currency');
            $table->json('rateplan')->nullable();
            $table->text('comment')->nullable();
            $table->dateTime('created_at_api')->nullable();
            $table->dateTime('updated_at_api')->nullable();
            
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
