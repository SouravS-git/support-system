<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\User;

it('allows customers to load the create ticket page', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $this->actingAs($customer)
        ->get(route('tickets.create'))
        ->assertOk();
});

it('allows customers to create tickets', function () {
    $this->freezeTime();

    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $this->actingAs($customer)
        ->post('/tickets', [
            'subject' => 'This is the subject of a ticket',
            'description' => 'This is the description of a ticket',
            'priority' => TicketPriority::LOW->value,
        ]);

    $this->assertDatabaseHas('tickets', [
        'created_by' => $customer->id,
        'subject' => 'This is the subject of a ticket',
        'description' => 'This is the description of a ticket',
        'priority' => TicketPriority::LOW,
        'status' => TicketStatus::OPEN,
        'sla_due_at' => now()->addHours(24),
    ]);
});

it('prevents agents to load the create ticket page', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $this->actingAs($agent)
        ->get(route('tickets.create'))
        ->assertForbidden();
});

it('prevents agents from creating tickets', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $this->actingAs($agent)
        ->post('/tickets', [
            'subject' => 'This is the subject of a ticket',
            'description' => 'This is the description of a ticket',
            'priority' => Ticketpriority::LOW->value,
        ])->assertForbidden();
});
