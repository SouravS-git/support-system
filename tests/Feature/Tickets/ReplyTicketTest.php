<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;

it('allows customers to reply to their own tickets', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $ticket = Ticket::factory()->for($customer, 'creator')->create();

    $this->actingAs($customer)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is the message of a reply',
        ])->assertRedirectBack();

    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $ticket->id,
        'user_id' => $customer->id,
        'message' => 'This is the message of a reply',
        'is_internal' => false,
    ]);
});

it('allows agents to reply to tickets assigned to them', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create();

    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is the message of a reply',
        ])->assertRedirectBack();

    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
        'message' => 'This is the message of a reply',
        'is_internal' => false,
    ]);
});

it('allows admins to reply to any ticket', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $ticket = Ticket::factory()->create();

    $this->actingAs($admin)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is the message of a reply',
        ])->assertRedirectBack();

    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $ticket->id,
        'user_id' => $admin->id,
        'message' => 'This is the message of a reply',
        'is_internal' => false,
    ]);
});

it('prevents customers from replying to tickets they did not create', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $ticket = Ticket::factory()->create();

    $this->actingAs($customer)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is the message of a reply',
        ])->assertForbidden();
});

it('prevents agents from replying to tickets they did not assign', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->create();

    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is the message of a reply',
        ])->assertForbidden();
});

it('allows agents to add internal replies to tickets', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create();

    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is an internal reply',
            'is_internal' => true,
        ])->assertRedirectBack();

    $this->assertDatabaseHas('ticket_replies', [
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
        'message' => 'This is an internal reply',
        'is_internal' => true,
    ]);
});

it('hides internal replies from customers', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $ticket = Ticket::factory()->for($customer, 'creator')->create();

    TicketReply::factory()->for($ticket)->create([
        'message' => 'This is an internal reply',
        'is_internal' => true,
    ]);

    $this->actingAs($customer)
        ->get(route('tickets.show', $ticket))
        ->assertSee($ticket->subject)
        ->assertDontSee('This is an internal reply');
});

it('changes the status of the ticket to in_progress when an agent replies to it for the first time', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create([
        'status' => TicketStatus::OPEN,
        'first_response_at' => null,
    ]);

    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is a reply',
            'is_internal' => false,
        ])
        ->assertRedirectBack();

    expect($ticket->fresh()->status)->toBe(TicketStatus::IN_PROGRESS);
});

it('also changes the status of the ticket to in_progress if the first reply of an agent is an internal reply', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create([
        'status' => TicketStatus::OPEN,
        'first_response_at' => null,
    ]);

    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is a reply',
            'is_internal' => true,
        ])
        ->assertRedirectBack();

    expect($ticket->fresh()->status)->toBe(TicketStatus::IN_PROGRESS);
});

it('prevents all the users to reply to a closed ticket', function () {
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::CLOSED,
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is a reply',
        ])->assertForbidden();
});

it('prevents customers to reply to a resolved ticket', function () {
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::RESOLVED,
    ]);

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $this->actingAs($customer)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is a reply',
        ])->assertForbidden();
});
