<?php

declare(strict_types=1);

namespace App\Listeners\TicketAssignment;

use App\Enums\TicketActivityType;
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
            'type' => TicketActivityType::ASSIGNED,
            'meta' => [
                'assigned_to' => $ticket->assigned_to,
            ],
        ]);
    }
}
