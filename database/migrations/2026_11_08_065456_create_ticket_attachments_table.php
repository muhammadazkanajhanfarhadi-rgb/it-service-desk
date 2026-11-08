<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                  ->constrained('tickets')->cascadeOnDelete();

            $table->foreignId('uploaded_by')
                  ->constrained('users')->restrictOnDelete();

            $table->string('original_name');    // original filename
            $table->string('stored_name');      // hashed/stored filename
            $table->string('file_path');        // path in storage
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size'); // bytes

            $table->timestamps();

            $table->index('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
