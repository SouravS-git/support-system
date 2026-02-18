@php
    use App\Enums\TicketActivityType;
    use App\Enums\TicketStatus;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ticket Details') }}
            </h2>
            <a href="{{ route('tickets.index') }}" class="text-sm text-gray-500 hover:text-gray-800">
                <x-secondary-button>My Tickets</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800">
                    <div class="min-w-full">
                        <h1 class="text-xl font-semibold mb-2">
                            Ticket #{{ $ticket->id }} — {{ $ticket->subject }}
                        </h1>

                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <x-status-label :status="$ticket->status">
                                    {{ $ticket->status->label() }}
                                </x-status-label>
                            </div>

                            <div class="h-3 w-px bg-slate-200"></div>

                            <div class="scale-90 origin-left">
                                <x-priority-label :priority="$ticket->priority">
                                    {{ $ticket->priority->label() }}
                                </x-priority-label>
                            </div>
                        </div>

                        @can('resolve', $ticket)
                            @if($ticket->canTransitionTo(TicketStatus::RESOLVED))
                                <div class="flex items-center justify-end">
                                    <form method="POST" action="{{ route('tickets.status.update', $ticket) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ TicketStatus::RESOLVED }}">
                                        <button class="bg-blue-600 text-white px-3 py-1 rounded">
                                            Mark as Resolved
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endcan

                        @can('close', $ticket)
                            @if($ticket->canTransitionTo(TicketStatus::CLOSED))
                                <div class="flex items-center justify-end">
                                    <form method="POST" action="{{ route('tickets.status.update', $ticket) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ TicketStatus::CLOSED }}">
                                        <button class="bg-gray-800 text-white px-3 py-1 rounded">
                                            Close Ticket
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endcan

                        <div class="pt-4">
                            <p>{{ $ticket->description }}</p>
                        </div>
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="text-sm font-medium text-gray-700">
                                Assigned Agent:
                                <span class="font-bold text-indigo-600 sm:inline">
                                    {{ $ticket->assignee->name ?? 'Not yet assigned' }}
                                </span>
                            </div>

                            @can('assign', $ticket)
                                <div class="sm:w-auto">
                                    <form action="{{ route('tickets.assignee.update', $ticket) }}" method="POST"
                                          class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                        @csrf
                                        @method('PATCH')

                                        <select name="agent_id"
                                                class="flex-1 text-sm rounded-md border-gray-300 shadow-inner transition-all focus:outline-2 focus:-outline-offset-2 focus:border-indigo-500 focus:ring-indigo-500 min-w-[200px]">
                                            <option value="">Select Agent</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}" @selected($ticket->assigned_to == $agent->id)>
                                                    {{ $agent->name }} ({{ $agent->email }})
                                                </option>
                                            @endforeach
                                        </select>

                                        <button type="submit"
                                                class="whitespace-nowrap rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                                            Assign
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>

                        <div class="flex items-center justify-end mt-1">
                            @error('agent_id')
                            <x-input-error :messages="$message"></x-input-error>
                            @enderror
                        </div>
                    </div>

                    {{-- Section to show activity log --}}
                    <div class="mt-4 bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="font-bold text-gray-800">Activity Log</h3>
                            </div>
                        </div>

                        <div class="p-8">
                            <div class="relative space-y-0">
                                @foreach($ticket->activities as $activity)
                                    @php
                                        $isLatest = $loop->last;
                                        $isClosed = $ticket->status->value === 'closed';
                                    @endphp

                                    <div class="relative flex gap-x-6 pb-10 last:pb-0">

                                        <div class="relative flex flex-col items-center flex-shrink-0 w-5">
                                            @if(!$loop->last)
                                                <div class="absolute top-6 w-0.5 h-full bg-gray-100">
                                                    {{-- The progress bar fills up to the latest point --}}
                                                    <div class="absolute top-0 w-full h-full bg-indigo-500"></div>
                                                </div>
                                            @endif

                                            <div class="z-10 flex h-5 w-5 items-center justify-center rounded-full border-2
                                                {{ $isLatest && !$isClosed ? 'border-indigo-600 ring-4 ring-indigo-50' : '' }}
                                                {{ $isLatest && $isClosed ? 'border-gray-300 bg-gray-50' : '' }}
                                                {{ !$isLatest ? 'border-indigo-500 bg-indigo-500' : '' }} transition-all duration-300">

                                                @if($isLatest)
                                                    @if($isClosed)
                                                        <div class="h-1.5 w-1.5 rounded-full bg-gray-400"></div>
                                                    @else
                                                        <div class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-pulse"></div>
                                                    @endif
                                                @else
                                                    <div class="h-1 w-1 rounded-full bg-white"></div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex-1 pt-0.5 min-w-0">
                                            <div class="flex justify-between items-start gap-4">
                                                <div class="truncate">
                                                    <h4 class="text-xs font-bold uppercase tracking-wide {{ $isLatest && $isClosed ? 'text-gray-400' : 'text-gray-900' }}">
                                                        @if($activity->type === TicketActivityType::STATUS_CHANGED)
                                                            {{ TicketStatus::from($activity->meta['to'])->label() }}
                                                        @else
                                                            {{ $activity->type->label() }}
                                                        @endif
                                                    </h4>
                                                    <p class="mt-1 text-xs {{ $isLatest && $isClosed ? 'text-gray-300' : 'text-gray-500' }} truncate">
                                                        {{ $activity->user->name }} • {{ $activity->created_at->format('M d, Y') }}
                                                    </p>
                                                </div>
                                                <time class="text-[10px] font-bold text-gray-400 tabular-nums uppercase whitespace-nowrap">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Section to show comments --}}
                    <div class="min-w-full mt-4">
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden flex flex-col">

                            <div
                                class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                    <h3 class="font-bold text-gray-800">Discussion Threads</h3>
                                </div>
                                @if (auth()->user()->isAgent() || auth()->user()->isAdmin())
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $ticket->replies->count() }} Comments</span>
                                @else
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $ticket->replies->where('is_internal', false)->count() }} Comments</span>
                                @endif
                            </div>

                            @if($ticket->replies()->count() > 0)
                                <div
                                    x-data
                                    x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
                                    class="h-[600px] overflow-y-auto p-6 space-y-8 bg-white"
                                >
                                    @foreach ($ticket->replies as $reply)
                                        @can('view', $reply)
                                            <div
                                                class="flex gap-4 {{ $reply->user_id === request()->user()->id ? 'flex-row-reverse' : '' }}">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 border border-gray-300 flex items-center justify-center shadow-sm">
                                                        <span
                                                            class="text-gray-700 font-bold text-sm">{{ strtoupper(substr($reply->user->name, 0, 1)) }}</span>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex flex-col max-w-[85%] {{ $reply->user_id === request()->user()->id ? 'items-end' : 'items-start' }}">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span
                                                            class="text-sm font-bold text-gray-800">{{ $reply->user->name }}</span>
                                                        <span
                                                            class="text-[11px] text-gray-400 font-medium">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>

                                                    @if ($reply->is_internal)
                                                        <div
                                                            class="p-4 rounded-2xl shadow-sm text-sm leading-relaxed {{ $reply->user_id === request()->user()->id ? 'bg-red-600 text-white rounded-tr-none' : 'bg-red-600 text-white rounded-tl-none' }}">
                                                            {{ $reply->message }}
                                                        </div>
                                                    @else
                                                        <div
                                                            class="p-4 rounded-2xl shadow-sm text-sm leading-relaxed {{ $reply->user_id === request()->user()->id ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-gray-100 text-gray-800 rounded-tl-none border border-gray-200' }}">
                                                            {{ $reply->message }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endcan
                                    @endforeach
                                </div>
                            @endif

                            @can('create', [\App\Models\TicketReply::class, $ticket])
                                <div class="p-6 bg-gray-50 border-t border-gray-200">
                                    <form action="{{ route('tickets.replies.store', $ticket) }}" method="POST"
                                          class="space-y-4">
                                        @csrf
                                        <div class="relative">
                                            <textarea
                                                name="message"
                                                rows="5"
                                                placeholder="Share your thoughts or updates..."
                                                class="w-full p-4 rounded-xl border border-gray-300 shadow-inner transition-all focus:outline-2 focus:-outline-offset-2 focus:border-indigo-500 focus:ring-indigo-500 text-sm resize-none"
                                                required
                                            ></textarea>
                                            @error('message')
                                            <x-input-error messages="{{ $message }}"></x-input-error>
                                            @enderror
                                        </div>

                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                            <div>
                                                @if (auth()->user()->isAgent() || auth()->user()->isAdmin())
                                                    <label class="inline-flex items-center cursor-pointer group">
                                                        <div class="relative">
                                                            <input type="checkbox" name="is_internal" value="1"
                                                                   class="sr-only peer">
                                                            <div
                                                                class="w-5 h-5 bg-white border-2 border-gray-300 rounded peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all"></div>
                                                            <svg
                                                                class="absolute w-3.5 h-3.5 text-white top-0.5 left-0.5 opacity-0 peer-checked:opacity-100 transition-opacity"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="4">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </div>
                                                        <span
                                                            class="ml-3 text-sm font-medium text-gray-600 group-hover:text-gray-800 transition-colors">Internal Note</span>
                                                    </label>
                                                    @error('is_internal')
                                                    <x-input-error messages="{{ $message }}"></x-input-error>
                                                    @enderror
                                                @endif
                                            </div>
                                            <button type="submit"
                                                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                Post Comment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
