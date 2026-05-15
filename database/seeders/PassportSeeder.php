<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Client;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Personal Access Client already exists
        if (!Client::where('personal_access_client', 1)->exists()) {
            Artisan::call('passport:client', [
                '--personal' => true,
                '--name' => 'Vynix Personal Access Client',
                '--no-interaction' => true,
            ]);
        }

        // Check if Password Grant Client already exists
        if (!Client::where('password_client', 1)->exists()) {
            Artisan::call('passport:client', [
                '--password' => true,
                '--name' => 'Vynix Password Grant Client',
                '--no-interaction' => true,
            ]);
        }
    }
}
