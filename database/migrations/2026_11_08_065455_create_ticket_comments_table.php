<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                  ->constrained('tickets')->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')->restrictOnDelete();

            $table->text('body');

            // 'public' = visible to requester, 'internal' = IT staff only
            $table->enum('type', ['public', 'internal'])->default('public');

            $table->timestamps();

            $table->index('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_comments');
    }
};
