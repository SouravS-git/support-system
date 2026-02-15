<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Ticket;
use App\TicketStatus;

class ResolveTicketAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(Ticket $ticket): void
    {
        $ticket->transitionTo(TicketStatus::RESOLVED);
    }
}
