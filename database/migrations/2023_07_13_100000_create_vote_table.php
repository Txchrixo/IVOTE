<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Elections.
 * Skipped when the table already exists (databases created before migrations were tracked).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vote')) {
            return;
        }

        Schema::create('vote', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->unsignedInteger('number');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('stage')->default(0)->index();
            $table->string('bg')->nullable();
            $table->dateTime('start_veriff')->nullable();
            $table->dateTime('end_veriff')->nullable();
            $table->dateTime('start_vote')->nullable();
            $table->dateTime('end_vote')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote');
    }
};
