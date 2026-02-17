<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets;

use App\Actions\Tickets\UpdateTicketAssigneeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\UpdateTicketAssigneeRequest;
use App\Models\Ticket;
use Illuminate\Support\Facades\Gate;

class TicketAssigneeController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketAssigneeRequest $request, UpdateTicketAssigneeAction $action, Ticket $ticket)
    {
        Gate::authorize('assign', $ticket);

        $ticket = $action->handle($request->validated(), $ticket);

        return redirect()->back();

    }
}
