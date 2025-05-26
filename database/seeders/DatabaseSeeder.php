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
        // Seed default admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@sipandi.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Add more seeders if necessary
        // Example:
        // $this->call(OtherSeeder::class);
    }
}
