<?php

use App\Models\User;

it('allows customers to create tickets', function () {
    $user = User::factory()->create([
        'role' => 'customer',
    ]);

    $response = $this->actingAs($user)
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

it('prevents agents from creating tickets', function () {
    $user = User::factory()->create([
        'role' => 'agents',
    ]);

    $response = $this->actingAs($user)
        ->post('/tickets', [
            'subject' => 'This is the subject of a ticket',
            'description' => 'This is the description of a ticket',
            'priority' => 'low',
        ]);

    $response->assertForbidden();
});
