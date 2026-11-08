<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_feedback', function (Blueprint $table) {
            $table->id();

            $table->foreignId('article_id')
                  ->constrained('knowledge_articles')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            $table->boolean('is_helpful');

            $table->timestamps();

            // Prevent duplicate feedback from same user on same article
            $table->unique(['article_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_feedback');
    }
};
