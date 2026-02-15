<?php

use App\Jobs\CheckSlaBreachJob;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\SlaBreachedNotification;

it('does not send duplicate sla breach notifications', function () {
    Notification::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $ticket = Ticket::factory()->create([
        'sla_due_at' => now()->subHour(),
    ]);

    // Run the job once
    CheckSlaBreachJob::dispatch($ticket);

    // Check if the notification was sent
    Notification::assertSentTo($admin, SlaBreachedNotification::class);

    // Run the job again
    CheckSlaBreachJob::dispatch($ticket);

    // Check if the notification was sent only once
    Notification::assertSentToTimes($admin, SlaBreachedNotification::class, 1);
});
