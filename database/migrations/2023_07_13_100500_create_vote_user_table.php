<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Elections a user took part in.
 * Foreign key columns follow the type of the referenced ids, which are signed
 * INT on databases created before migrations were tracked.
 * Skipped when the table already exists.
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
            $this->foreignKey($table, 'user_id', 'users');
            $this->foreignKey($table, 'vote_id', 'vote');
            $table->unique(['user_id', 'vote_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_user');
    }

    private function foreignKey(Blueprint $table, string $column, string $parent): void
    {
        $type = strtolower(Schema::getColumnType($parent, 'id', true));
        $legacy = str_starts_with($type, 'int') && !str_contains($type, 'unsigned');

        ($legacy ? $table->integer($column) : $table->unsignedBigInteger($column));
        $table->foreign($column)->references('id')->on($parent)->cascadeOnDelete();
    }
};
