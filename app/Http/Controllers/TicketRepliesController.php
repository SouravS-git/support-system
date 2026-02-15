<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketReplyRequest;
use App\Http\Requests\UpdateTicketReplyRequest;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Policies\TicketReplyPolicy;
use Illuminate\Support\Facades\Gate;

class TicketRepliesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): void
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketReplyRequest $request, Ticket $ticket)
    {
        Gate::authorize('create', [TicketReply::class, $ticket]);

        $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
            'is_internal' => $request->validated('is_internal') ?? false,
        ]);

        // SLA Stops when the agent makes the first response, and it's true even for internal replies
        if ($request->user()->isAgent() && $ticket->first_response_at === null) {
            $ticket->update(['first_response_at' => now()]);
        }

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketReply $ticketReply): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketReply $ticketReply): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketReplyRequest $request, TicketReply $ticketReply): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketReply $ticketReply): void
    {
        //
    }
}
