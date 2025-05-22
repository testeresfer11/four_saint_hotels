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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotel_id');
            $table->string('reservation_code');
            $table->bigInteger('payment_id')->unique();
            $table->string('customer_name');
            $table->decimal('price', 10, 2);
            $table->dateTime('payment_date_time');
            $table->string('payment_method');
            $table->string('payment_source');
            $table->string('currency');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
