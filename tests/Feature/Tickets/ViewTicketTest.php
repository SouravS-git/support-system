<?php

use App\Models\Ticket;
use App\Models\User;

it('allows customers to see their own tickets', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $ownTicket = Ticket::factory()->for($customer, 'creator')->create();
    $otherTicket = Ticket::factory()->create();

    $this->actingAs($customer)
        ->get(route('tickets.index'))
        ->assertSee($ownTicket->subject)
        ->assertDontSee($otherTicket->subject);
});

it('allows agents to see tickets assigned to them', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $assignedTicket = Ticket::factory()->for($agent, 'assignee')->create();
    $otherTicket = Ticket::factory()->create();

    $this->actingAs($agent)
        ->get(route('tickets.index'))
        ->assertSee($assignedTicket->subject)
        ->assertDontSee($otherTicket->subject);
});

it('allows admins to see all the tickets', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $ticket = Ticket::factory()->create();

    $this->actingAs($admin)
        ->get(route('tickets.index'))
        ->assertSee($ticket->subject);
});

it('prevents customers from viewing details of tickets created by others', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $ticket = Ticket::factory()->create();

    $this->actingAs($customer)
        ->get(route('tickets.show', $ticket))
        ->assertForbidden();
});

it('prevents agents from viewing details of tickets assigned to others', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->create([
        'assigned_to' => User::factory()->create(['role' => 'agent'])->id,
    ]);

    $this->actingAs($agent)
        ->get(route('tickets.show', $ticket))
        ->assertForbidden();
});

it('allows admins to view details of all the tickets', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $ticket = Ticket::factory()->create();

    $this->actingAs($admin)
        ->get(route('tickets.show', $ticket))
        ->assertSee($ticket->subject);
});
