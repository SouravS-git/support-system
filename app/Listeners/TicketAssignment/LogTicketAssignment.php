<?php

namespace App\Listeners\TicketAssignment;

use App\Events\TicketAssigned;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class LogTicketAssignment
{
    /**
     * Create the event listener.
     */
    public function __construct(#[CurrentUser] protected User $user)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketAssigned $event): void
    {
        $ticket = $event->ticket;

        $ticket->activities()->create([
            'user_id' => $this->user->id,
            'type' => 'ticket_assigned',
            'meta' => [
                'assigned_to' => $ticket->assigned_to,
            ],
        ]);
    }
}
