<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();

            // Link to category — nullOnDelete karena service bisa berdiri sendiri
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();

            // SLA policy untuk layanan ini (opsional)
            $table->foreignId('sla_policy_id')
                  ->nullable()
                  ->constrained('sla_policies')
                  ->nullOnDelete();

            // Icon untuk UI (Material Symbol name)
            $table->string('icon', 60)->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
