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
<<<<<<<< HEAD:database/migrations/2025_04_10_144645_create_contacts_table.php
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->text('reply')->nullable();
            $table->tinyInteger('is_replied')->default(0);
========
        Schema::create('help_desks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ticket_id')->unique();
            $table->longText('title');
            $table->longText('description')->nullable();
            $table->enum('priority',['Low','Medium','High'])->default('Low');
            $table->enum('status',['Pending','In Progress','Done'])->default('Pending');
            $table->softDeletes();
>>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321:database/migrations/2024_06_03_043013_create_help_desks_table.php
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:database/migrations/2025_04_10_144645_create_contacts_table.php
        Schema::dropIfExists('contacts');
========
        Schema::dropIfExists('help_desks');
>>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321:database/migrations/2024_06_03_043013_create_help_desks_table.php
    }
};
