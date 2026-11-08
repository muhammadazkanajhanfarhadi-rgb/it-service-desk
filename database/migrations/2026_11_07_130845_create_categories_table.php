<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('icon', 60)->nullable();       // Material Symbol icon name
            $table->string('description')->nullable();
            $table->string('color', 20)->nullable();      // hex color for UI
            $table->unsignedBigInteger('parent_id')->nullable(); // null = top-level category
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')
                  ->references('id')
                  ->on('categories')
                  ->nullOnDelete();

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
