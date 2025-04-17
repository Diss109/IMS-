<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Complaint;
use App\Models\User;

class ComplaintsSeeder extends Seeder
{
    public function run(): void
    {
        // Get a user with commercial_maritime role for assignment
        $commercialMaritime = User::where('role', 'commercial_maritime')->first();

        $complaints = [
            [
                'company_name' => 'Maritime Express SARL',
                'first_name' => 'Mohamed',
                'last_name' => 'Ben Ali',
                'email' => 'mohamed.benali@maritime-express.tn',
                'phone' => '+216 98 123 456',
                'complaint_type' => 'retard_livraison',
                'description' => 'Le conteneur MRSK12345 est arrivé avec 3 jours de retard, causant des perturbations importantes dans notre chaîne de production.',
                'urgency_level' => 'high',
                'status' => 'en_attente',
                'assigned_to' => $commercialMaritime?->id,
                'created_at' => now()->subDays(2),
            ],
            [
                'company_name' => 'Trans Med Shipping',
                'first_name' => 'Sami',
                'last_name' => 'Trabelsi',
                'email' => 'sami.trabelsi@transmed.com.tn',
                'phone' => '+216 55 789 123',
                'complaint_type' => 'marchandise_endommagée',
                'description' => 'Lors de la réception de notre cargaison réf TM789, nous avons constaté que 30% de la marchandise est endommagée à cause d\'une mauvaise manipulation.',
                'urgency_level' => 'high',
                'status' => 'résolu',
                'assigned_to' => $commercialMaritime?->id,
                'admin_notes' => 'Client remboursé et incident documenté. Formation supplémentaire planifiée pour l\'équipe de manutention.',
                'created_at' => now()->subDays(5),
            ],
            [
                'company_name' => 'Global Logistics Tunisia',
                'first_name' => 'Leila',
                'last_name' => 'Mansour',
                'email' => 'l.mansour@glt.tn',
                'phone' => '+216 29 456 789',
                'complaint_type' => 'retard_chargement',
                'description' => 'Retard important lors du chargement au port de Radès. L\'opération a pris 5 heures au lieu d\'1 heure prévue.',
                'urgency_level' => 'medium',
                'status' => 'non_résolu',
                'assigned_to' => $commercialMaritime?->id,
                'created_at' => now()->subDay(),
            ],
            [
                'company_name' => 'Maghreb Shipping Services',
                'first_name' => 'Karim',
                'last_name' => 'Gharbi',
                'email' => 'k.gharbi@mss.com.tn',
                'phone' => '+216 92 345 678',
                'complaint_type' => 'mauvais_comportement',
                'description' => 'Comportement inapproprié de l\'agent lors de la réception des documents. Manque de professionnalisme et retard dans le traitement du dossier.',
                'urgency_level' => 'low',
                'status' => 'en_attente',
                'assigned_to' => $commercialMaritime?->id,
                'created_at' => now()->subHours(12),
            ],
        ];

        foreach ($complaints as $complaint) {
            Complaint::create($complaint);
        }
    }
}
