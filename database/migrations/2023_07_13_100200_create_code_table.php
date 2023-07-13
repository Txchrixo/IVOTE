<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Voting codes handed to registered voters.
 * Skipped when the table already exists (databases created before migrations were tracked).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('code')) {
            return;
        }

        Schema::create('code', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->boolean('is_used')->default(false);
            $table->text('public_key')->nullable();
            $table->foreignId('item_id')->constrained('vote')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code');
    }
};
