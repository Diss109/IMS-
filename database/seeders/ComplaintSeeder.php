<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get a commercial maritime user
        $commercialUser = User::firstOrCreate(
            ['email' => 'commercial@example.com'],
            [
                'name' => 'Commercial Maritime',
                'password' => bcrypt('password'),
                'role' => 'commercial_maritime'
            ]
        );

        $complaints = [
            [
                'company_name' => 'Société de Transport Maritime',
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@stm.com',
                'phone' => '+212 6XX-XXXXXX',
                'complaint_type' => 'retard_livraison',
                'urgency_level' => 'high',
                'description' => 'Livraison retardée de 5 jours sur le port de Casablanca. Impact sur notre chaîne de production.',
                'status' => 'en_attente',
                'assigned_to' => $commercialUser->id,
            ],
            [
                'company_name' => 'Logistique Maroc SA',
                'first_name' => 'Fatima',
                'last_name' => 'Benali',
                'email' => 'fatima.benali@logmaroc.ma',
                'phone' => '+212 6XX-XXXXXX',
                'complaint_type' => 'marchandise_endommagée',
                'urgency_level' => 'critical',
                'description' => 'Conteneur endommagé lors du déchargement à Tanger Med. Produits électroniques endommagés.',
                'status' => 'en_attente',
                'assigned_to' => $commercialUser->id,
            ],
            [
                'company_name' => 'Import-Export Atlantique',
                'first_name' => 'Mohammed',
                'last_name' => 'El Amrani',
                'email' => 'm.elamrani@ieatlantique.ma',
                'phone' => '+212 6XX-XXXXXX',
                'complaint_type' => 'retard_chargement',
                'urgency_level' => 'medium',
                'description' => 'Retard de chargement au port de Jorf Lasfar. Perte de créneau de départ.',
                'status' => 'en_attente',
                'assigned_to' => $commercialUser->id,
            ],
            [
                'company_name' => 'Transit International',
                'first_name' => 'Amina',
                'last_name' => 'Zouhair',
                'email' => 'amina.z@transit.ma',
                'phone' => '+212 6XX-XXXXXX',
                'complaint_type' => 'mauvais_comportement',
                'urgency_level' => 'high',
                'description' => 'Comportement inapproprié du personnel lors des opérations de dédouanement.',
                'status' => 'en_attente',
                'assigned_to' => $commercialUser->id,
            ]
        ];

        foreach ($complaints as $complaint) {
            Complaint::create($complaint);
        }
    }
}
