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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            // Client Details
            $table->string('company_name');
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');

            // Complaint Details
            $table->enum('complaint_type', [
                'service_quality',
                'delivery_delay',
                'damaged_goods',
                'customer_service',
                'billing_issue',
                'other'
            ]);
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('description');

            // Tracking and Status
            $table->enum('status', [
                'pending',
                'under_review',
                'in_progress',
                'resolved',
                'closed'
            ])->default('pending');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();

            // Relationships
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('service_provider_id')->nullable()->constrained('service_providers')->nullOnDelete();
            $table->foreignId('transporter_id')->nullable()->constrained('transporters')->nullOnDelete();

            // Timestamps and Soft Delete
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
