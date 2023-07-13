<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Candidates of an election.
 * Skipped when the table already exists (databases created before migrations were tracked).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('candidate')) {
            return;
        }

        Schema::create('candidate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vote_id')->constrained('vote')->cascadeOnDelete();
            $table->string('name', 135);
            $table->string('des', 500)->nullable();
            $table->string('cni')->nullable();
            $table->string('photo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate');
    }
};
