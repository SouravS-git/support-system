<?php

declare(strict_types=1);

namespace App\Listeners\TicketCreation;

use App\Events\TicketCreated;
use App\Notifications\TicketCreation\TicketCreationNotificationForCreator;

class SendTicketCreationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;
        $ticket->creator->notify(new TicketCreationNotificationForCreator($ticket));
    }
}
