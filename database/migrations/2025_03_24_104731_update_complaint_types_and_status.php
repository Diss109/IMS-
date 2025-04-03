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
        Schema::table('complaints', function (Blueprint $table) {
            // Drop the existing columns
            $table->dropColumn(['complaint_type', 'status']);
        });

        Schema::table('complaints', function (Blueprint $table) {
            // Recreate with new values in French
            $table->enum('complaint_type', [
                'retard_livraison',
                'retard_chargement',
                'marchandise_endommagée',
                'mauvais_comportement',
                'autre'
            ])->after('last_name');

            $table->enum('status', [
                'en_attente',
                'résolu',
                'non_résolu'
            ])->default('en_attente')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Drop the modified columns
            $table->dropColumn(['complaint_type', 'status']);
        });

        Schema::table('complaints', function (Blueprint $table) {
            // Restore original columns in English
            $table->enum('complaint_type', [
                'delivery_delay',
                'loading_delay',
                'damaged_goods',
                'bad_behavior',
                'other'
            ])->after('last_name');

            $table->enum('status', [
                'pending',
                'solved',
                'unsolved'
            ])->default('pending')->after('description');
        });
    }
};
