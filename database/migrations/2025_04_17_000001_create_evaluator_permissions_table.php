<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluator_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('service_provider_type'); // e.g., armateur, compagnie_aerienne, etc.
            $table->string('role'); // e.g., commercial_routier, exploitation_maritime, etc.
            $table->timestamps();
            $table->unique(['service_provider_type', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluator_permissions');
    }
};
