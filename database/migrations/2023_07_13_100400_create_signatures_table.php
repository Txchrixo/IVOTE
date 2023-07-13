<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ring signatures published with ballots.
 * Skipped when the table already exists (databases created before migrations were tracked).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('signatures')) {
            return;
        }

        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->string('sig_hash', 64)->unique();
            $table->longText('sig_msg');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
