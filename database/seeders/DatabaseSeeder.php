<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Medicine;
use App\Models\ExpirationMedicine;
use App\Models\PaymentStatus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
           //Medicine::factory(10)->create();
            ExpirationMedicine::factory(10)->create();
            $this->call([
               OrderStatusSeeder::class,
               PaymentStatusSeeder::class
           ]);
    }
}
