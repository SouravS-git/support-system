<?php

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

    $reply = TicketReply::factory()->for($ticket)->create([
        'message' => 'This is an internal reply',
        'is_internal' => true,
    ]);

    $this->actingAs($customer)
        ->get(route('tickets.show', $ticket))
        ->assertSee($ticket->subject)
        ->assertDontSee('This is an internal reply');
});
