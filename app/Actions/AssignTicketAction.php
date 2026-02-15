<?php

declare(strict_types=1);

namespace App\Actions;

use App\Events\TicketAssigned;
use App\Models\Ticket;
use App\TicketStatus;
use RuntimeException;

class AssignTicketAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(array $validatedData, Ticket $ticket): Ticket
    {
        $updated = $ticket->update([
            'assigned_to' => $validatedData['agent_id'],
            'status' => TicketStatus::IN_PROGRESS->value,
        ]);

        if (! $updated) {
            throw new RuntimeException('Ticket assignment failed');
        }

        TicketAssigned::dispatch($ticket);

        return $ticket;
    }
}
