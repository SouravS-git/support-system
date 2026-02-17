<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\TicketSlaBreached;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckSlaBreachJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Mark sla overdue tickets as breached
        Ticket::whereNull('first_response_at')
            ->whereNull('sla_breached_at')
            ->where('sla_due_at', '<', now())
            ->update([
                'sla_breached_at' => now(),
            ]);

        // 2. Notify admins for newly breached tickets
        Ticket::whereNotNull('sla_breached_at')
            ->whereNull('sla_notified_at')
            ->each(function (Ticket $ticket) {
                User::where('role', 'admin')->each(function (User $admin) use ($ticket) {
                    // Creates an activity log and sends an email notification to the admin
                    TicketSlaBreached::dispatch($ticket, $admin);
                });

                $ticket->update(['sla_notified_at' => now()]);
            });
    }
}
