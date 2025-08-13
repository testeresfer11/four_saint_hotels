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
        Schema::create('hotel_room_other_service_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_room_id')
                ->constrained('hotel_rooms')
                ->onDelete('cascade')
                ->name('fk_hotel_room_id'); // Custom constraint name for hotel_room_id
            
            $table->foreignId('other_service_category_id')
                ->constrained('other_service_categories')
                ->onDelete('cascade')
                ->name('fk_other_service_category_id'); // Custom constraint name for other_service_category_id
            
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_room_other_service_category');
    }
};
