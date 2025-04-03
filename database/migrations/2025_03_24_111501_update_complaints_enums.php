<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, modify the complaint_type enum
        DB::statement("ALTER TABLE complaints MODIFY COLUMN complaint_type ENUM(
            'retard_livraison',
            'retard_chargement',
            'marchandise_endommagée',
            'mauvais_comportement',
            'autre'
        ) NOT NULL");

        // Then, modify the status enum
        DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM(
            'en_attente',
            'résolu',
            'non_résolu'
        ) NOT NULL DEFAULT 'en_attente'");

        // Update any existing records to use new values
        DB::table('complaints')->update([
            'status' => 'en_attente'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the complaint_type enum
        DB::statement("ALTER TABLE complaints MODIFY COLUMN complaint_type ENUM(
            'service_quality',
            'delivery_delay',
            'damaged_goods',
            'customer_service',
            'billing_issue',
            'other'
        ) NOT NULL");

        // Revert the status enum
        DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM(
            'pending',
            'under_review',
            'in_progress',
            'resolved',
            'closed'
        ) NOT NULL DEFAULT 'pending'");

        // Update any existing records to use old values
        DB::table('complaints')->update([
            'status' => 'pending'
        ]);
    }
};
