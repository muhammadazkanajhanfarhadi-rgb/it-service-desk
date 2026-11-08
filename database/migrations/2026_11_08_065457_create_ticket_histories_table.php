<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                  ->constrained('tickets')->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')->restrictOnDelete();

            // Action type: status_changed, assigned, priority_changed, comment_added, etc.
            $table->string('action', 60);

            // What changed
            $table->string('field_changed', 60)->nullable();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();

            // Human-readable description shown in timeline
            $table->string('description');

            $table->timestamp('created_at')->useCurrent();

            $table->index('ticket_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_histories');
    }
};
