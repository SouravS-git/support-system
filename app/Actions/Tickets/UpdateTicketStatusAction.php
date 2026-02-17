<?php

declare(strict_types=1);

namespace App\Actions\Tickets;

use App\Enums\TicketStatus;
use App\Events\TicketStatusChanged;
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
        $oldStatus = $ticket->status;

        $ticket->transitionTo(TicketStatus::from($validated['status']));

        $newStatus = $ticket->status;

        // Creates an activity log and sends email notifications to the assigned agent and customer
        TicketStatusChanged::dispatch($ticket, $oldStatus, $newStatus, 'both');
    }
}
