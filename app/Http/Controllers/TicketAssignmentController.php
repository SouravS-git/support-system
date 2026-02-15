<?php

namespace App\Http\Controllers;

use App\Actions\AssignTicketAction;
use App\Http\Requests\AssignTicketRequest;
use App\Models\Ticket;
use Illuminate\Support\Facades\Gate;

class TicketAssignmentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(AssignTicketRequest $request, AssignTicketAction $action, Ticket $ticket)
    {
        Gate::authorize('assign', $ticket);

        $ticket = $action->handle($request->validated(), $ticket);

        return redirect()->route('tickets.show', $ticket);

    }
}
