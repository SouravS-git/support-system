<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CloseTicketAction;
use App\Actions\ResolveTicketAction;
use App\Models\Ticket;
use Illuminate\Support\Facades\Gate;

class TicketStatusController extends Controller
{
    public function resolve(ResolveTicketAction $action, Ticket $ticket)
    {
        Gate::authorize('resolve', $ticket);

        $action->handle($ticket);

        return redirect()->back();
    }

    public function close(CloseTicketAction $action, Ticket $ticket)
    {
        Gate::authorize('close', $ticket);

        $action->handle($ticket);

        return redirect()->back();
    }
}
