<?php

declare(strict_types=1);

namespace App\Actions\Tickets;

use App\Enums\TicketPriority;
use App\Events\TicketCreated;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class CreateTicketAction
{
    public function __construct(#[CurrentUser] protected User $user) {}

    public function handle(array $validatedData): Ticket
    {
        $ticket = Ticket::create([
            'created_by' => $this->user->id,
            'subject' => $validatedData['subject'],
            'description' => $validatedData['description'],
            'priority' => TicketPriority::from($validatedData['priority']),
            'sla_due_at' => now()->addHours(
                match ($validatedData['priority']) {
                    TicketPriority::HIGH->value => 1,
                    TicketPriority::MEDIUM->value => 4,
                    default => 24
                }
            ),
        ]);

        // Creates an activity log and sends email notifications to the creator
        TicketCreated::dispatch($ticket);

        return $ticket;
    }
}
