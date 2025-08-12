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
        Schema::create('hotel_room_type_service_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_room_type_id')
                  ->constrained('hotel_room_types','room_type_id')
                  ->onDelete('cascade');
            $table->foreignId('service_category_id')
                  ->constrained()
                  ->onDelete('cascade');
          });
          
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_room_type_service_category');
    }
};
