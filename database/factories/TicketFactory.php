<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $priority = $this->faker->randomElement(TicketPriority::cases())->value;
        $status = $this->faker->randomElement(TicketStatus::cases())->value;

        return [
            'created_by' => User::factory(),
            'subject' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(10),
            'priority' => $priority,
            'status' => $status,
            'sla_due_at' => match ($priority) {
                TicketPriority::HIGH->value => now()->addHours(1),
                TicketPriority::MEDIUM->value => now()->addHours(4),
                default => now()->addHours(24),
            },
        ];
    }
}
