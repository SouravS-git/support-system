<?php

use App\Models\User;

it('allows customers to load the create ticket page', function () {
    $user = User::factory()->create([
        'role' => 'customer',
    ]);

    $this->actingAs($user)
        ->get(route('tickets.create'))
        ->assertOk();
});

it('allows customers to create tickets', function () {
    $user = User::factory()->create([
        'role' => 'customer',
    ]);

    $this->actingAs($user)
        ->post('/tickets', [
            'subject' => 'This is the subject of a ticket',
            'description' => 'This is the description of a ticket',
            'priority' => 'low',
        ]);

    $this->assertDatabaseHas('tickets', [
        'created_by' => $user->id,
        'subject' => 'This is the subject of a ticket',
        'description' => 'This is the description of a ticket',
        'priority' => 'low',
        'status' => 'open',
        'sla_due_at' => now()->addHours(24),
    ]);
});

it('prevents agents to load the create ticket page', function () {
    $user = User::factory()->create([
        'role' => 'agent',
    ]);

    $this->actingAs($user)
        ->get(route('tickets.create'))
        ->assertForbidden();
});

it('prevents agents from creating tickets', function () {
    $user = User::factory()->create([
        'role' => 'agent',
    ]);

    $this->actingAs($user)
        ->post('/tickets', [
            'subject' => 'This is the subject of a ticket',
            'description' => 'This is the description of a ticket',
            'priority' => 'low',
        ])->assertForbidden();
});
