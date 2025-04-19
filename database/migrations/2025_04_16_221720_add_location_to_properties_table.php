<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {

            if (!Schema::hasColumn('properties', 'governorate_id')) {
                $table->foreignId('governorate_id')
                    ->nullable()
                    ->after('location')
                    ->constrained('governorates')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('properties', 'city_id')) {
                $table->foreignId('city_id')
                    ->nullable()
                    ->after('governorate_id')
                    ->constrained('cities')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
      
            $table->dropForeign(['city_id']);
            $table->dropForeign(['governorate_id']);
            
      
            if (Schema::hasColumn('properties', 'city_id')) {
                $table->dropColumn('city_id');
            }
            
            if (Schema::hasColumn('properties', 'governorate_id')) {
                $table->dropColumn('governorate_id');
            }
        });
    }
};