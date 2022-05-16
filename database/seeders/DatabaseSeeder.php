<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LanguagesTableSeeder::class);

        if (env('APP_ENV') == "testing") {
            return true;
        }

        $dummy = $this->command->ask('Do you want to create example data? (y/n) ', 'n');
        if (strtolower($dummy) == 'y') {
            $this->call(DummyDataSeeder::class);
        }
    }
}
