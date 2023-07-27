<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('code', function (Blueprint $table) {
            $table->string('registered_key', 66)->nullable()->unique();
            $table->uuid('verification_reference')->nullable()->unique();
            $table->uuid('verification_session')->nullable()->unique();
            $table->text('verification_url')->nullable();
            $table->timestamp('identity_verified_at')->nullable();
        });

        Schema::table('vote', function (Blueprint $table) {
            $table->json('ballot_definition')->nullable();
        });

        Schema::create('ballots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('vote')->cascadeOnDelete();
            $table->unsignedBigInteger('candidate_id');
            $table->string('key_image', 66);
            $table->json('signature');
            $table->string('receipt', 64)->unique();
            $table->string('bitcoin_txid', 64)->nullable();
            $table->text('bitcoin_transaction')->nullable();
            $table->timestamp('bitcoin_confirmed_at')->nullable();
            $table->timestamps();
            $table->unique(['election_id', 'key_image']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ballots');
        Schema::table('vote', fn (Blueprint $table) => $table->dropColumn('ballot_definition'));
        Schema::table('code', function (Blueprint $table) {
            $table->dropUnique(['registered_key']);
            $table->dropUnique(['verification_reference']);
            $table->dropUnique(['verification_session']);
            $table->dropColumn(['registered_key', 'verification_reference', 'verification_session', 'verification_url', 'identity_verified_at']);
        });
    }
};
