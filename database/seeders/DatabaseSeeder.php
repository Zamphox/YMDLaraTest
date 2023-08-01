<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(env('SEEDING_USER_ENTRY_COUNT', 5))
            ->hasCompanies(env('SEEDING_COMPANY_ENTRY_COUNT', 10))
            ->create();
    }
}
