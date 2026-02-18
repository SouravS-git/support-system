<?php

declare(strict_types=1);

namespace App\Listeners\SlaBreach;

use App\Enums\TicketActivityType;
use App\Events\TicketSlaBreached;

class LogTicketSlaBreach
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

        $ticket->activities()->create([
            'user_id' => null,
            'type' => TicketActivityType::SLA_BREACHED,
        ]);
    }
}
