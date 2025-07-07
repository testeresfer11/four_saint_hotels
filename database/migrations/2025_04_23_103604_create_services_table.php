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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id');
            $table->string('service_name');
            $table->string('service_category_name')->nullable();
            $table->string('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('included')->default(false);
            $table->boolean('compulsory')->default(false);
            $table->string('price_type')->nullable();
            $table->string('price_applicable')->nullable();
            $table->string('billing_type')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('vat', 5, 2)->nullable();
            $table->boolean('apply_city_tax')->default(false);
            $table->string('currency')->nullable();
            $table->json('available_rateplans'); // Store rateplans as JSON
            $table->timestamps();

            // Foreign key constraint if necessary
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
