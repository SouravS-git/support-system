<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssignment\TicketAssignmentNotificationForAssignee;
use App\Notifications\TicketAssignment\TicketAssignmentNotificationForCreator;

it('allows admin to assign tickets to agents', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->create();

    $this->actingAs($admin)
        ->patch(route('tickets.assignee.update', $ticket), [
            'agent_id' => $agent->id,
        ])->assertRedirectBack();

});

it('prevents non-admins from assigning tickets to agents', function () {
    $ticket = Ticket::factory()->create();

    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $this->actingAs($agent)
        ->patch(route('tickets.assignee.update', $ticket), [
            'agent_id' => $agent->id,
        ])->assertForbidden();
});
