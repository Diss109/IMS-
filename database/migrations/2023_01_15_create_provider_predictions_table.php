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
        Schema::create('provider_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->float('predicted_score', 8, 2)->comment('The predicted performance score');
            $table->float('confidence_level', 8, 4)->comment('Confidence level in the prediction (0-1)');
            $table->timestamp('prediction_date');
            $table->string('prediction_period')->comment('next_month, next_quarter, etc.');
            $table->json('factors')->nullable()->comment('Factors that influenced the prediction');
            $table->string('model_version')->comment('Version of the prediction model used');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_predictions');
    }
};
