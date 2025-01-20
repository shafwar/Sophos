<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed default user
        User::factory()->create([
            'name' => 'Operator',
            'email' => 'operator@example.com',
            'password' => bcrypt('12345'),
        ]);

        // Add more seeders if necessary
        // Example:
        // $this->call(OtherSeeder::class);
    }
}
