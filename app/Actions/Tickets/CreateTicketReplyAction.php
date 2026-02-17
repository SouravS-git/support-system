<?php

declare(strict_types=1);

namespace App\Actions\Tickets;

use App\Enums\TicketStatus;
use App\Events\TicketStatusChanged;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class CreateTicketReplyAction
{
    public function __construct(#[CurrentUser] protected User $user) {}

    public function handle(array $validatedData, Ticket $ticket): TicketReply
    {
        $reply = $ticket->replies()->create([
            'user_id' => $this->user->id,
            'message' => $validatedData['message'],
            'is_internal' => $validatedData['is_internal'] ?? false,
        ]);

        // SLA Stops when the agent makes the first response, and it's true even for internal replies
        if ($this->user->isAgent() && ! $ticket->hasFirstResponse()) {
            $ticket->update(['first_response_at' => now()]);
        }

        // No status changes if the reply is internal
        if (! $reply->is_internal) {

            if ($this->user->isAgent()) {
                // Status changes if the ticket is IN_PROGRESS
                if ($ticket->status === TicketStatus::IN_PROGRESS) {
                    $ticket->transitionTo(TicketStatus::WAITING_FOR_CUSTOMER);

                    // Creates an activity log and sends an email notification to the customer
                    TicketStatusChanged::dispatch($ticket, TicketStatus::IN_PROGRESS, TicketStatus::WAITING_FOR_CUSTOMER, 'creator');
                }
            }

            if ($this->user->isCustomer()) {
                // Status changes if the ticket is WAITING_FOR_CUSTOMER
                if ($ticket->status === TicketStatus::WAITING_FOR_CUSTOMER && $ticket->hasFirstResponse()) {
                    $ticket->transitionTo(TicketStatus::IN_PROGRESS);

                    // Creates an activity log and sends an email notification to the assigned agent
                    TicketStatusChanged::dispatch($ticket, TicketStatus::WAITING_FOR_CUSTOMER, TicketStatus::IN_PROGRESS, 'assignee');
                }
            }
        }

        return $reply;
    }
}
