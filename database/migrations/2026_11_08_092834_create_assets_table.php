<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            // Unique inventory number: AST-00001
            $table->string('asset_number', 20)->unique();
            $table->string('name');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->unique();

            // Category link (nullable — some assets may not fit a category)
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();

            // Specs
            $table->text('specifications')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_end')->nullable();

            // Status: available, assigned, maintenance, retired
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])
                  ->default('available');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
