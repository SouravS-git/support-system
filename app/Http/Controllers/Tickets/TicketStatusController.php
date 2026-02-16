<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets;

use App\Actions\Tickets\UpdateTicketStatusAction;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\UpdateTicketStatusRequest;
use App\Models\Ticket;
use Illuminate\Support\Facades\Gate;

class TicketStatusController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketStatusRequest $request, UpdateTicketStatusAction $action, Ticket $ticket)
    {
        match ($request->validated('status')) {
            TicketStatus::RESOLVED->value => Gate::authorize('resolve', $ticket),
            TicketStatus::CLOSED->value => Gate::authorize('close', $ticket),
            default => abort(403),
        };

        $action->handle($request->validated(), $ticket);

        return redirect()->back();

    }
}
