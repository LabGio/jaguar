<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            Schema::table('pages', function (Blueprint $table) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('pages')
                    ->nullOnDelete();
                $table->index(['parent_id', 'is_published', 'menu_order']);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropIndex(['parent_id', 'is_published', 'menu_order']);
                $table->dropConstrainedForeignId('parent_id');
            });
        });
    }
};
