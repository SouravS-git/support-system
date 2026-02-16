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
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::IN_PROGRESS,
    ]);

    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $this->actingAs($agent)
        ->patch(route('tickets.status.update', $ticket), [
            'status' => TicketStatus::RESOLVED->value,
        ])
        ->assertRedirectBack();

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
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::RESOLVED,
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
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

it('changes the status of the ticket to waiting_for_customer when an agent replies to it', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create([
        'status' => TicketStatus::IN_PROGRESS,
    ]);

    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is a reply',
            'is_internal' => false,
        ])
        ->assertRedirectBack();

    expect($ticket->fresh()->status)->toBe(TicketStatus::WAITING_FOR_CUSTOMER);
});

it('does not change the status of the ticket to waiting_for_customer when an agent posts an internal reply', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create([
        'status' => TicketStatus::IN_PROGRESS,
    ]);

    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is a reply',
            'is_internal' => true,
        ])
        ->assertRedirectBack();

    expect($ticket->fresh()->status)->toBe(TicketStatus::IN_PROGRESS);
});

it('changes the status of the ticket to in_progress when a customer replies to it', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $ticket = Ticket::factory()->for($customer, 'creator')->create([
        'status' => TicketStatus::WAITING_FOR_CUSTOMER,
        'first_response_at' => now(),
    ]);

    $this->actingAs($customer)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is a reply',
        ])
        ->assertRedirectBack();

    expect($ticket->fresh()->status)->toBe(TicketStatus::IN_PROGRESS);
});

it('does not change the status of the ticket to in_progress when the agent has not responded to it yet', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $ticket = Ticket::factory()->for($customer, 'creator')->create([
        'status' => TicketStatus::OPEN,
        'first_response_at' => null,
    ]);

    $this->actingAs($customer)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is a reply',
        ])
        ->assertRedirectBack();

    expect($ticket->fresh()->status)->toBe(TicketStatus::OPEN);
});
