<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Foreign keys — added after name column
            $table->foreignId('role_id')
                  ->after('id')
                  ->constrained('roles')
                  ->restrictOnDelete();

            $table->foreignId('department_id')
                  ->nullable()
                  ->after('role_id')
                  ->constrained('departments')
                  ->nullOnDelete();

            $table->foreignId('location_id')
                  ->nullable()
                  ->after('department_id')
                  ->constrained('locations')
                  ->nullOnDelete();

            // Extra profile fields
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('job_title', 100)->nullable()->after('avatar');
            $table->boolean('is_active')->default(true)->after('job_title');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn(['phone', 'avatar', 'job_title', 'is_active']);
        });
    }
};
