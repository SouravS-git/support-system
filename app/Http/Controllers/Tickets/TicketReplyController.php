<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets;

use App\Actions\Tickets\CreateTicketReplyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\StoreTicketReplyRequest;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Gate;

class TicketReplyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketReplyRequest $request, CreateTicketReplyAction $action, Ticket $ticket)
    {
        Gate::authorize('create', [TicketReply::class, $ticket]);

        $action->handle($request->validated(), $ticket);

        return redirect()->back();
    }
}
