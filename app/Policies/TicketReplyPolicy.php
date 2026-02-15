<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;

class TicketReplyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TicketReply $ticketReply): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()){
            return true;
        }

        if ($user->isAgent()){
            return $ticket->assigned_to === $user->id;
        }

        return $ticket->created_by === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TicketReply $ticketReply): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TicketReply $ticketReply): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TicketReply $ticketReply): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TicketReply $ticketReply): bool
    {
        return false;
    }
}
