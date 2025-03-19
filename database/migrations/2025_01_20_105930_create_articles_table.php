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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان المقال
            $table->string('slug')->unique(); // الرابط الخاص بالمقال
            $table->text('content'); // محتوى المقال
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete(); // القسم
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // ناشر المقال
            $table->enum('status', ['draft', 'published'])->default('draft'); // حالة المقال
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
