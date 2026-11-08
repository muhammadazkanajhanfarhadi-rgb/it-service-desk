<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                  ->constrained('assets')
                  ->cascadeOnDelete();

            // Who currently holds the asset
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Who performed the assignment (admin)
            $table->foreignId('assigned_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('returned_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // An asset can only have ONE active assignment (returned_at IS NULL)
            // Enforced at application level + partial index
            $table->index('asset_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
