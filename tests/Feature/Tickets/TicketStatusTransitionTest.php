<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

it('prevents invalid transitions', function () {
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::OPEN,
    ]);

    expect(fn () => $ticket->transitionTo(TicketStatus::CLOSED))
        ->toThrow(DomainException::class);
});

it('allows agents to mark tickets as resolved', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create([
        'status' => TicketStatus::IN_PROGRESS,
    ]);

    $this->actingAs($agent)
        ->patch(route('tickets.status.update', $ticket), [
            'status' => TicketStatus::RESOLVED->value,
        ])->assertRedirectBack();

    expect($ticket->fresh()->status)
        ->toBe(TicketStatus::RESOLVED);

});

it('prevents non-agents from resolving tickets', function () {
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::IN_PROGRESS,
    ]);

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $this->actingAs($customer)
        ->patch(route('tickets.status.update', $ticket), [
            'status' => TicketStatus::RESOLVED->value,
        ])
        ->assertForbidden();

});

it('allows admins to mark tickets as closed', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create([
        'status' => TicketStatus::RESOLVED,
    ]);

    $this->actingAs($admin)
        ->patch(route('tickets.status.update', $ticket), [
            'status' => TicketStatus::CLOSED->value,
        ])
        ->assertRedirectBack();

    expect($ticket->fresh()->status)
        ->toBe(TicketStatus::CLOSED);
});

it('prevents non-admins from closing tickets', function () {
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::RESOLVED,
    ]);

    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $this->actingAs($agent)
        ->patch(route('tickets.status.update', $ticket), [
            'status' => TicketStatus::CLOSED->value,
        ])
        ->assertForbidden();
});
