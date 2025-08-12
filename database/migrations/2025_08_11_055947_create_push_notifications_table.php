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
       Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receiver_id')->index();
            $table->string('title');
            $table->text('body');
            $table->text('image');
            $table->string('type')->nullable(); // e.g., message, alert, etc.
            $table->string('notification_type')->nullable(); // custom classification
            $table->timestamps();

            $table->foreign('receiver_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
    }
};
