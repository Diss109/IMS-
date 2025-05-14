<?php

namespace Database\Seeders;

use App\Models\ServiceProvider;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ServiceProviderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $serviceTypes = array_keys(ServiceProvider::getTypes());

        for ($i = 0; $i < 90; $i++) {
            $companyName = $faker->company;
            ServiceProvider::create([
                'name' => $companyName,
                'email' => $faker->unique()->companyEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'service_type' => $faker->randomElement($serviceTypes),
                'status' => $faker->randomElement(['active', 'inactive', 'suspended']),
                'description' => $faker->optional(0.7)->paragraph(),
                'contact_person' => $faker->optional(0.6)->name(),
            ]);
        }
    }
}
