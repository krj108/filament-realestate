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
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('governorate_id')
                ->nullable()
            ->after('location')
            ->constrained('governorates')
            ->cascadeOnDelete();
            $table->foreignId('city_id')
            ->nullable()
            ->after('governorate_id')
            ->constrained('cities')
            ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropForeign(['governorate_id']);
            $table->dropColumn(['city_id', 'governorate_id']);
        });
    }
};
