<?php

declare(strict_types=1);

namespace App\Actions\Tickets;

use App\Enums\TicketPriority;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class CreateTicketAction
{
    public function __construct(#[CurrentUser] protected User $user) {}

    public function handle(array $validatedData): Ticket
    {
        return Ticket::create([
            'created_by' => $this->user->id,
            'subject' => $validatedData['subject'],
            'description' => $validatedData['description'],
            'priority' => TicketPriority::from($validatedData['priority'])->value,
            'sla_due_at' => now()->addHours(
                match ($validatedData['priority']) {
                    TicketPriority::HIGH->value => 1,
                    TicketPriority::MEDIUM->value => 4,
                    default => 24
                }
            ),
        ]);
    }
}
