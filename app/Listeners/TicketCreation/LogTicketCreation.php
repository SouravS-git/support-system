<?php

namespace App\Listeners\TicketCreation;

use App\Enums\TicketActivityType;
use App\Events\TicketCreated;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class LogTicketCreation
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
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;

        $ticket->activities()->create([
            'user_id' => $this->user->id,
            'type' => TicketActivityType::CREATED,
        ]);
    }
}
