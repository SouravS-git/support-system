<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $agent = User::factory()->create([
            'name' => 'Agent User',
            'email' => 'agent@example.com',
            'role' => 'agent',
        ]);

        $customer = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'role' => 'customer',
        ]);

        $tickets = Ticket::factory(100)->create([
            'created_by' => $customer->id,
            'assigned_to' => $agent->id,
        ]);

        for ($i = 0; $i < 100; $i++) {
            TicketReply::factory(10)->recycle($tickets)->create([
                'user_id' => $agent->id,
            ]);

            TicketReply::factory(10)->recycle($tickets)->create([
                'user_id' => $customer->id,
                'is_internal' => false,
            ]);

            TicketReply::factory(10)->recycle($tickets)->create([
                'user_id' => $admin->id,
                'is_internal' => true,
            ]);
        }
    }
}
