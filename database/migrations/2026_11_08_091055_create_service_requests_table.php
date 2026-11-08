<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();

            // Human-readable request number: REQ-00001
            $table->string('request_number', 20)->unique();

            // Relations
            $table->foreignId('service_id')
                  ->constrained('services')
                  ->restrictOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Approval workflow
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Status: pending_approval, approved, rejected, in_progress, completed, cancelled
            $table->enum('status', [
                'pending_approval',
                'approved',
                'rejected',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('pending_approval');

            $table->text('description')->nullable();

            // Dynamic form data (JSON) — menyimpan field dinamis per jenis layanan
            $table->json('request_data')->nullable();

            // Approval tracking
            $table->text('approval_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('service_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
