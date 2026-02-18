<?php

declare(strict_types=1);

namespace App\Listeners\SlaBreach;

use App\Events\TicketSlaBreached;
use App\Notifications\SlaBreach\TicketSlaBreachNotification;

class SendTicketSlaBreachNotification
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
    public function handle(TicketSlaBreached $event): void
    {
        $ticket = $event->ticket;
        $admin = $event->admin;

        $admin->notify(new TicketSlaBreachNotification($ticket));
    }
}
