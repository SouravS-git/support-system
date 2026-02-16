<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;

it('allows admin to assign tickets to agents', function () {

    Notification::fake();

    $ticket = Ticket::factory()->create();
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $this->actingAs($admin)
        ->patch(route('tickets.assignee.update', $ticket), [
            'agent_id' => $agent->id,
        ])->assertRedirectToRoute('tickets.show', $ticket);

    expect($ticket->fresh()->assigned_to)->toBe($agent->id)
        ->and($ticket->fresh()->status)->toBe(TicketStatus::IN_PROGRESS);

    Notification::assertSentTo($agent, TicketAssignedNotification::class);
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
