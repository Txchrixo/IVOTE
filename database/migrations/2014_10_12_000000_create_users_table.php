<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Accounts of the election authority (EA), registration authority (RA) and voters.
 * Skipped when the table already exists (databases created before migrations were tracked).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64)->unique();
            $table->string('nickname')->nullable();
            $table->string('password');
            $table->unsignedTinyInteger('role')->index();
            $table->string('bitcoin_address', 90)->nullable();
            $table->text('bitcoin_priv_key')->nullable();
            $table->string('eth_address', 90)->nullable();
            $table->text('eth_priv_key')->nullable();
            $table->string('solana_address', 90)->nullable();
            $table->text('solana_priv_key')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
