<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Identity verification (Veriff) sessions.
 * Skipped when the table already exists (databases created before migrations were tracked).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('verification_sessions')) {
            return;
        }

        Schema::create('verification_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('sessionId')->unique();
            $table->string('hmacSignature');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_sessions');
    }
};
