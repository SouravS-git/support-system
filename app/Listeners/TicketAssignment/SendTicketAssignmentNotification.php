<?php

declare(strict_types=1);

namespace App\Listeners\TicketAssignment;

use App\Events\TicketAssigned;
use App\Notifications\TicketAssignment\TicketAssignmentNotificationForAssignee;
use App\Notifications\TicketAssignment\TicketAssignmentNotificationForCreator;

class SendTicketAssignmentNotification
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
    public function handle(TicketAssigned $event): void
    {
        $ticket = $event->ticket;
        $ticket->creator->notify(new TicketAssignmentNotificationForCreator($ticket));
        $ticket->assignee->notify(new TicketAssignmentNotificationForAssignee($ticket));
    }
}
