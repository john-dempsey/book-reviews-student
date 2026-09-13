<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table.
     *
     * Five fixed, named accounts rather than random ones - easier to
     * recognise while browsing the seeded catalogue, and to log in as
     * while testing.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Emma Watson', 'email' => 'emma.watson84@example.com'],
            ['name' => "Liam O'Connor", 'email' => 'liam.oconnor22@example.com'],
            ['name' => 'Sophia Chen', 'email' => 'sophia.chen91@example.com'],
            ['name' => 'Carlos Silva', 'email' => 'carlos.silva33@example.com'],
            ['name' => 'Aisha Patel', 'email' => 'aisha.patel55@example.com'],
        ];

        foreach ($users as $user) {
            User::factory()->create($user);
        }
    }
}
