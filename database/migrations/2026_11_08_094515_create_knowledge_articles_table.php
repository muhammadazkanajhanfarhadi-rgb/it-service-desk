<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();

            // Content stored as sanitized HTML
            $table->longText('content');

            // Featured image path (nullable)
            $table->string('featured_image')->nullable();

            // Category (reuse existing categories table)
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();

            // Author
            $table->foreignId('author_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Status: draft, published, archived
            $table->enum('status', ['draft', 'published', 'archived'])
                  ->default('draft');

            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();

            $table->index('category_id');
            $table->index('status');
            $table->index('published_at');
            $table->fullText(['title', 'content']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_articles');
    }
};
