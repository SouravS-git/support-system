<?php

use App\Jobs\CheckSlaBreachJob;
use App\Models\Ticket;
use App\Models\User;

it('marks ticket as sla breached when overdue', function () {
    $this->freezeTime();

    $ticket = Ticket::factory()->create([
        'sla_due_at' => now()->subHour(),
        'first_response_at' => null,
        'sla_breached_at' => null,
    ]);

    CheckSlaBreachJob::dispatch($ticket);

    expect($ticket->fresh()->sla_breached_at)
        ->not()
        ->toBeNull();
});

it('stops sla when agent responds', function () {
    $this->freezeTime();

    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $ticket = Ticket::factory()->for($agent, 'assignee')->create([
        'sla_due_at' => now()->subminute(),
        'first_response_at' => null,
        'sla_breached_at' => null,
    ]);

    $this->actingAs($agent)
        ->post(route('tickets.replies.store', $ticket), [
            'message' => 'This is a reply',
        ])->assertRedirectBack();

    CheckSlaBreachJob::dispatch($ticket);

    expect($ticket->fresh()->first_response_at)->not()->toBeNull()
        ->and($ticket->fresh()->sla_breached_at)->toBeNull();

});
