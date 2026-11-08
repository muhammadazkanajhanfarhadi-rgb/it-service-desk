<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('priority_id')
                  ->constrained('priorities')
                  ->restrictOnDelete();

            // SLA targets in minutes
            $table->unsignedInteger('response_minutes');    // target first response
            $table->unsignedInteger('resolution_minutes');  // target resolution

            // Business hours config
            $table->boolean('business_hours_only')->default(false);
            $table->time('business_start')->default('08:00:00');
            $table->time('business_end')->default('17:00:00');

            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
    }
};
