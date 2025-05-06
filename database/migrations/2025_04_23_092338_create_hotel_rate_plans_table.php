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
       Schema::create('hotel_rate_plans', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('hotel_id');
        $table->unsignedBigInteger('rateplan_id')->unique(); // from API
        $table->string('rateplan_name');
        $table->boolean('linked_to_master')->default(false);
        $table->unsignedBigInteger('linked_to_rateplan_id')->nullable();
        $table->string('price_model')->nullable();
        $table->boolean('dynamic_pricing')->default(false);
        $table->timestamps();

        $table->foreign('hotel_id')->references('hotel_id')->on('hotels')->onDelete('cascade');
    });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rate_plans');
    }
};
