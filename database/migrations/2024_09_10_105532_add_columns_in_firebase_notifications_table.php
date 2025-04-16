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
        Schema::table('firebase_notifications', function (Blueprint $table) {
            $table->string('title')->after('user_id')->nullable();
            $table->string('body')->after('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firebase_notifications', function (Blueprint $table) {
            $table->dropColumn(['title','body']);
        });
    }
};
