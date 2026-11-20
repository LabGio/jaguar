<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::table('pages')->where(function ($query): void {
            $query->whereIn('slug', ['en', 'de'])->orWhere('slug', 'like', 'en/%')->orWhere('slug', 'like', 'de/%');
        })->exists()) {
            throw new RuntimeException('Gli slug en e de sono riservati alle lingue: rinominare le pagine in conflitto prima della migrazione.');
        }

        Schema::table('pages', function (Blueprint $table) {
            $table->json('translations')->nullable();
        });

        Schema::table('page_images', function (Blueprint $table) {
            $table->json('translations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('translations');
        });

        Schema::table('page_images', function (Blueprint $table) {
            $table->dropColumn('translations');
        });
    }
};
