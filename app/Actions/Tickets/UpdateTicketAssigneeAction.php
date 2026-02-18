<?php

declare(strict_types=1);

namespace App\Actions\Tickets;

use App\Enums\TicketStatus;
use App\Events\TicketAssigned;
use App\Models\Ticket;
use RuntimeException;

class UpdateTicketAssigneeAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(array $validatedData, Ticket $ticket)
    {
        // To prevent duplicate assignment
        if($ticket->assigned_to != $validatedData['agent_id']){

            $ticket->update([
                'assigned_to' => $validatedData['agent_id'],
            ]);

            // Creates an activity log and sends email notifications to the assigned agent and customer
            TicketAssigned::dispatch($ticket);
        }

        return $ticket;
    }
}
