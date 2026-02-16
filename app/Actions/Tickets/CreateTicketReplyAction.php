<?php

declare(strict_types=1);

namespace App\Actions\Tickets;

use App\Enums\TicketStatus;
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

        if ($this->user->isAgent()) {

            // SLA Stops when the agent makes the first response, and it's true even for internal replies
            if (! $ticket->hasFirstResponse()) {
                $ticket->update(['first_response_at' => now()]);
            }

            // Status does not change if the reply is internal
            if (! $reply->is_internal && $ticket->canTransitionTo(TicketStatus::WAITING_FOR_CUSTOMER)) {
                $ticket->transitionTo(TicketStatus::WAITING_FOR_CUSTOMER);
            }
        }

        // Status starts changing to in_progress only after the agent makes the first response
        if ($this->user->isCustomer() && $ticket->hasFirstResponse() && $ticket->canTransitionTo(TicketStatus::IN_PROGRESS)) {
            $ticket->transitionTo(TicketStatus::IN_PROGRESS);
        }

        return $reply;
    }
}
