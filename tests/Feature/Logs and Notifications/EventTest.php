<?php

use App\Enums\TicketStatus;
use App\Jobs\CheckSlaBreachJob;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\SlaBreach\TicketSlaBreachNotification;
use App\Notifications\TicketAssignment\TicketAssignmentNotificationForAssignee;
use App\Notifications\TicketAssignment\TicketAssignmentNotificationForCreator;
use App\Notifications\TicketStatusChange\TicketStatusChangeNotificationForAssignee;
use App\Notifications\TicketStatusChange\TicketStatusChangeNotificationForCreator;

it('creates a log and notifies admin when ticket sla breaches', function () {
    $this->freezeTime();
    Notification::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $ticket = Ticket::factory()->create([
        'sla_due_at' => now()->subHour(),
        'first_response_at' => null,
        'sla_breached_at' => null,
    ]);

    // Running the job twice to ensure it's sending the notification only once
    CheckSlaBreachJob::dispatch($ticket);
    CheckSlaBreachJob::dispatch($ticket);

    expect($ticket->fresh()->sla_breached_at)
        ->not()
        ->toBeNull();

    $this->assertDatabaseHas('ticket_activities', [
        'ticket_id' => $ticket->id,
        'user_id' => null,
        'type' => 'sla_breached',
        'meta' => null
    ]);
    $this->assertDatabaseCount('ticket_activities', 1);

    Notification::assertSentToTimes($admin, TicketSlaBreachNotification::class, 1);

});

it('creates a log and notifies both the assignee and creator when the ticket is assigned', function () {
    Notification::fake();

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

    expect($ticket->fresh()->assigned_to)->toBe($agent->id)
        ->and($ticket->fresh()->status)->toBe(TicketStatus::IN_PROGRESS);

    $this->assertDatabaseHas('ticket_activities', [
        'ticket_id' => $ticket->id,
        'user_id' => $admin->id,
        'type' => 'ticket_assigned',
        'meta' => json_encode([
            'assigned_to' => $agent->id,
        ])
    ]);

    Notification::assertSentTo($agent, TicketAssignmentNotificationForAssignee::class);
    Notification::assertSentTo($ticket->creator, TicketAssignmentNotificationForCreator::class);

});

it('creates a log and notifies the creator when agent replies', function () {
    Notification::fake();

    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create([
        'status' => TicketStatus::IN_PROGRESS,
    ]);

    // Replying twice to ensure the log and notification are created only once for consecutive replies
    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is the first message',
        ])->assertRedirectBack();
    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is the second message',
        ])->assertRedirectBack();

    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
        'message' => 'This is the first message',
    ]);
    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
        'message' => 'This is the second message',
    ]);

    $this->assertDatabaseHas('ticket_activities', [
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
        'type' => 'status_changed',
        'meta' => json_encode([
            'from' => TicketStatus::IN_PROGRESS,
            'to' => TicketStatus::WAITING_FOR_CUSTOMER
        ])
    ]);
    $this->assertDatabaseCount('ticket_activities', 1);

    Notification::assertSentToTimes($ticket->creator, TicketStatusChangeNotificationForCreator::class, 1);
});

it('creates a log and notifies the assignee when customer replies', function () {
    Notification::fake();
    $this->freezeTime();

    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $ticket = Ticket::factory()->for($customer, 'creator')->create([
        'assigned_to' => $agent->id,
        'status' => TicketStatus::WAITING_FOR_CUSTOMER,
        'first_response_at' => now(),
    ]);

    // Replying twice to ensure the log and notification are created only once for consecutive replies
    $this->actingAs($customer)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is the first message',
        ])->assertRedirectBack();
    $this->actingAs($customer)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is the second message',
        ])->assertRedirectBack();

    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $ticket->id,
        'user_id' => $customer->id,
        'message' => 'This is the first message',
    ]);
    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $ticket->id,
        'user_id' => $customer->id,
        'message' => 'This is the second message',
    ]);

    $this->assertDatabaseHas('ticket_activities', [
        'ticket_id' => $ticket->id,
        'user_id' => $customer->id,
        'type' => 'status_changed',
        'meta' => json_encode([
            'from' => TicketStatus::WAITING_FOR_CUSTOMER,
            'to' => TicketStatus::IN_PROGRESS
        ])
    ]);
    $this->assertDatabaseCount('ticket_activities', 1);

    Notification::assertSentToTimes($ticket->assignee, TicketStatusChangeNotificationForAssignee::class, 1);

});
