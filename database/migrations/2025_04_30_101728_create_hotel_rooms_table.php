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
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id')->unique(); // from API
            $table->unsignedBigInteger('hotel_id');
            $table->unsignedBigInteger('room_type_id'); // FK to room type
            $table->string('room_name');
            $table->timestamps();

            $table->foreign('hotel_id')->references('hotel_id')->on('hotels')->onDelete('cascade');
            $table->foreign('room_type_id')->references('room_type_id')->on('hotel_room_types')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
