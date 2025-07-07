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
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained();
            $table->unsignedBigInteger('service_id');
            $table->string('service_name');
            $table->string('category_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('included')->default(false);
            $table->boolean('compulsory')->default(false);
            $table->string('price_type');
            $table->string('price_applicable');
            $table->string('billing_type')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};
