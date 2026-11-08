<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenance', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                  ->constrained('assets')
                  ->cascadeOnDelete();

            // Type: preventive, corrective, upgrade, inspection
            $table->enum('type', ['preventive', 'corrective', 'upgrade', 'inspection'])
                  ->default('preventive');

            $table->text('description')->nullable();

            // Who performed the maintenance
            $table->string('technician', 100)->nullable();
            $table->foreignId('performed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->date('maintenance_date');
            $table->decimal('cost', 12, 2)->default(0);

            $table->timestamps();

            $table->index('asset_id');
            $table->index('maintenance_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance');
    }
};
