<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained('service_providers')->onDelete('cascade');
            $table->float('predicted_score');
            $table->float('confidence_level');
            $table->dateTime('prediction_date');
            $table->string('prediction_period'); // e.g., 'next_month', 'next_quarter', etc.
            $table->json('factors')->nullable(); // Storing factors affecting prediction
            $table->string('model_version')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_predictions');
    }
};
