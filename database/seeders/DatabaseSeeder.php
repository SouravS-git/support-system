<?php

namespace Database\Seeders;

use App\Enums\TicketActivityType;
use App\Models\Ticket;
use App\Models\TicketActivity;
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

        TicketActivity::factory(10)->create([
            'ticket_id' => Ticket::factory([
                'created_by' => $customer->id,
            ]),
            'user_id' => $customer->id,
            'type' => TicketActivityType::CREATED,
        ]);
    }
}
