<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();         // 'Critical', 'High', 'Medium', 'Low'
            $table->string('slug', 50)->unique();         // 'critical', 'high', 'medium', 'low'
            $table->tinyInteger('level');                 // 1=Critical, 2=High, 3=Medium, 4=Low
            $table->string('color', 30)->nullable();      // badge color token e.g. 'status-danger'
            $table->string('icon', 60)->nullable();       // Material Symbol icon name
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('priorities');
    }
};
