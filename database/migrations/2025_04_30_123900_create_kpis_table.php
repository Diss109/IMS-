<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('target', 10, 2);
            $table->decimal('current_value', 10, 2)->default(0);
            $table->string('period')->default('monthly'); // daily, weekly, monthly, quarterly, yearly
            $table->text('description')->nullable();
            $table->string('category')->default('general'); // general, complaints, staff, customer_satisfaction
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpis');
    }
};
