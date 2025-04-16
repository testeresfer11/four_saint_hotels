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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('day_count')->after('plan_type')->default(1);
            $table->tinyInteger('is_notified')->after('day_count')->nullable();
            $table->date('scratched_date')->after('is_notified')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['day_count','is_notified','scratched_date']);
        });
    }
};
