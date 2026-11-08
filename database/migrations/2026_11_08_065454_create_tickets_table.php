<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Unique human-readable ID: INC-00001
            $table->string('ticket_number', 20)->unique();

            // Core fields
            $table->string('title');
            $table->text('description');

            // Status workflow
            $table->enum('status', [
                'new', 'assigned', 'in_progress',
                'pending', 'resolved', 'closed', 'reopened',
            ])->default('new');

            // Relations
            $table->foreignId('requester_id')
                  ->constrained('users')->restrictOnDelete();

            $table->foreignId('assignee_id')
                  ->nullable()
                  ->constrained('users')->nullOnDelete();

            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')->nullOnDelete();

            $table->foreignId('priority_id')
                  ->nullable()
                  ->constrained('priorities')->nullOnDelete();

            $table->foreignId('sla_policy_id')
                  ->nullable()
                  ->constrained('sla_policies')->nullOnDelete();

            $table->foreignId('location_id')
                  ->nullable()
                  ->constrained('locations')->nullOnDelete();

            // Impact / Urgency
            $table->enum('impact', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');

            // SLA tracking
            $table->timestamp('response_due_at')->nullable();
            $table->timestamp('resolution_due_at')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->boolean('sla_response_breached')->default(false);
            $table->boolean('sla_resolution_breached')->default(false);

            // Resolution
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index('status');
            $table->index('requester_id');
            $table->index('assignee_id');
            $table->index('priority_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
