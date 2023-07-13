<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bitcoin addresses funded for voters.
 * Skipped when the table already exists (databases created before migrations were tracked).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('key')) {
            return;
        }

        Schema::create('key', function (Blueprint $table) {
            $table->id();
            $table->text('bitcoin_public_key');
            $table->text('bitcoin_private_key');
            $table->string('bitcoin_address', 90)->unique();
            $table->boolean('is_used')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->foreignId('used_for')->constrained('vote')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('key');
    }
};
