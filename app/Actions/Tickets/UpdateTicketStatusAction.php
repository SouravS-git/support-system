<?php

declare(strict_types=1);

namespace App\Actions\Tickets;

use App\Enums\TicketStatus;
use App\Models\Ticket;

class UpdateTicketStatusAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(array $validated, Ticket $ticket): void
    {
        $ticket->transitionTo(TicketStatus::from($validated['status']));
    }
}
