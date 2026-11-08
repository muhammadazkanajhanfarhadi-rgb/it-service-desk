<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Actor — nullable untuk aksi sistem (cron, queue, dll)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // What happened
            $table->string('action', 80);          // e.g. 'ticket.created', 'user.updated'
            $table->string('module', 60)->nullable(); // e.g. 'ticket', 'user', 'auth'
            $table->text('description')->nullable();

            // Polymorphic subject — what entity was affected
            $table->string('subject_type', 100)->nullable(); // e.g. 'App\Models\Ticket'
            $table->unsignedBigInteger('subject_id')->nullable();

            // Change tracking
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Request context
            $table->string('ip_address', 45)->nullable();    // IPv6 compatible
            $table->string('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();
            // No updated_at — audit logs are immutable

            // Indexes for common queries
            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
