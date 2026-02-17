<?php

namespace App\Listeners\TicketStatusChange;

use App\Events\TicketStatusChanged;
use App\Notifications\TicketStatusChange\TicketStatusChangeNotificationForAssignee;
use App\Notifications\TicketStatusChange\TicketStatusChangeNotificationForCreator;

class SendTicketStatusChangeNotification
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
    public function handle(TicketStatusChanged $event): void
    {
        $ticket = $event->ticket;
        $sendTo = $event->sendTo;

        if($sendTo == 'creator'){
            $ticket->creator->notify(new TicketStatusChangeNotificationForCreator($ticket));
        }

        elseif($sendTo == 'assignee'){
            $ticket->assignee->notify(new TicketStatusChangeNotificationForAssignee($ticket));
        }

        else{
            $ticket->creator->notify(new TicketStatusChangeNotificationForCreator($ticket));
            $ticket->assignee->notify(new TicketStatusChangeNotificationForAssignee($ticket));
        }
    }
}
