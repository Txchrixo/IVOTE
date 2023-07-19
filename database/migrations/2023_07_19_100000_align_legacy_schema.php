<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the columns the application expects to databases created before
 * migrations were tracked. Every change is skipped when already present.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'solana_address')) {
                $table->string('solana_address', 90)->nullable();
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
        });

        Schema::table('candidate', function (Blueprint $table) {
            if (!Schema::hasColumn('candidate', 'cni')) {
                $table->string('cni')->nullable();
            }
            if (!Schema::hasColumn('candidate', 'photo')) {
                $table->string('photo')->nullable();
            }
        });

        // Descriptions are validated up to 500 characters
        Schema::table('candidate', fn (Blueprint $table) => $table->string('des', 500)->nullable()->change());
    }

    public function down(): void
    {
        // Columns may hold data entered after the upgrade, they are kept.
    }
};
