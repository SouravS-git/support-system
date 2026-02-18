<?php

namespace App\Listeners\TicketStatusChange;

use App\Enums\TicketActivityType;
use App\Events\TicketStatusChanged;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class LogTicketStatusChange
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
    public function handle(TicketStatusChanged $event): void
    {
        $ticket = $event->ticket;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;

        $ticket->activities()->create([
            'user_id' => $this->user->id,
            'type' => TicketActivityType::STATUS_CHANGED,
            'meta' => [
                'from' => $oldStatus,
                'to' => $newStatus,
            ],
        ]);
    }
}
