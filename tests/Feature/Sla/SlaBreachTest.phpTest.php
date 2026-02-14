<?php

use App\Jobs\CheckSlaBreachJob;
use App\Models\Ticket;

it('marks ticket as sla breached when overdue', function () {
    $ticket = Ticket::factory()->create([
        'sla_due_at' => now()->subHour(),
        'priority' => 'high',
        'first_response_at' => null,
        'sla_breached_at' => null,
    ]);

    CheckSlaBreachJob::dispatch($ticket);

    expect($ticket->fresh()->sla_notified_at)->not()->toBeNull();
});
