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
        Schema::create('room_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('rateplan_id');
            $table->date('start_rate_date');
            $table->date('end_rate_date');
            $table->decimal('price', 10, 2);
            $table->integer('number_of_guests')->nullable();
            $table->string('currency', 5)->nullable();
            $table->timestamps();
    
            $table->index(['hotel_id', 'room_id', 'rateplan_id', 'start_rate_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_rates');
    }
};
