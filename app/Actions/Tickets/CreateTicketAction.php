<?php

declare(strict_types=1);

namespace App\Actions\Tickets;

use App\Models\Ticket;
use App\Models\User;
use App\TicketPriority;
use Illuminate\Container\Attributes\CurrentUser;

class CreateTicketAction
{
    public function __construct(#[CurrentUser] protected User $user) {}

    public function handle(array $data): Ticket
    {
        return Ticket::create([
            'created_by' => $this->user->id,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'priority' => TicketPriority::from($data['priority'])->value,
            'sla_due_at' => now()->addHours(
                match ($data['priority']) {
                    TicketPriority::HIGH->value => 1,
                    TicketPriority::MEDIUM->value => 4,
                    default => 24
                }
            ),
        ]);
    }
}
