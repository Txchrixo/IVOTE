<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Elections a user took part in.
 * Skipped when the table already exists (databases created before migrations were tracked).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vote_user')) {
            return;
        }

        Schema::create('vote_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vote_id')->constrained('vote')->cascadeOnDelete();
            $table->unique(['user_id', 'vote_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_user');
    }
};
