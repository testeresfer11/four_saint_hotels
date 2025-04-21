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
        Schema::create('gift_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('voucher_code')->unique();
            $table->decimal('amount', 8, 2);
            $table->date('expiry_date');
            $table->text('description');
            $table->enum('status', ['active', 'redeemed', 'expired', 'revoked'])->default('active');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_vouchers');
    }
};
