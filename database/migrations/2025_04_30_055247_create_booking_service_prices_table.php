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
        Schema::create('booking_service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_service_id')->constrained();
            $table->date('date');
            $table->integer('quantity');
            $table->decimal('vat', 8, 3);
            $table->decimal('city_tax', 8, 3);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_service_prices');
    }
};
