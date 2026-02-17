<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Tickets') }}
            </h2>
            <a href="{{ route('tickets.create') }}" class="text-sm text-gray-500 hover:text-gray-800">
                <x-secondary-button>Create a Ticket</x-secondary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800">
                    @if($tickets->count() > 0)
                        <div class="min-w-full mb-5">
                        <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-none [&::-webkit-scrollbar-track]:bg-scrollbar-track [&::-webkit-scrollbar-thumb]:bg-scrollbar-thumb">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8 py-12 px-6">
                                @foreach($tickets as $index => $ticket)
                                    <a href="{{ route('tickets.show', $ticket) }}" class="group relative block h-full">
                                        <div class="absolute -inset-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-[2.5rem] opacity-0 group-hover:opacity-10 transition duration-500 blur-xl"></div>

                                        <div class="relative flex flex-col h-full bg-white rounded-[2rem] border border-slate-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] transition-all duration-500 group-hover:border-indigo-100 overflow-hidden">

                                            <div class="px-8 pt-7 pb-2 flex items-center justify-between">
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

                                                <span class="text-[10px] font-bold text-slate-200 group-hover:text-indigo-200 transition-colors">Ticket #{{ $ticket->id }}</span>
                                            </div>

                                            <div class="px-8 py-5 flex-1">
                                                <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors duration-300 leading-tight">
                                                    {{ $ticket->subject }}
                                                </h3>
                                                <p class="mt-4 text-sm text-slate-500 leading-relaxed font-light line-clamp-2">
                                                    {{ $ticket->description }}
                                                </p>
                                            </div>

                                            <div class="mt-2 mx-2 mb-2 p-5 bg-slate-50/50 rounded-[1.5rem] border border-slate-100/50 flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-[inset_0_1px_2px_rgba(0,0,0,0.05)]">
                                                        <span class="text-[10px] font-black text-indigo-600">{{ strtoupper(substr(\App\Models\User::find($ticket->created_by)->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-[11px] font-bold text-slate-700 leading-none">{{ \App\Models\User::find($ticket->created_by)->name }}</span>
                                                        <span class="text-[9px] font-medium text-slate-400 mt-1 uppercase tracking-widest">{{ $ticket->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>

                                                <div class="w-8 h-8 rounded-full bg-white border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 group-hover:border-indigo-100 group-hover:shadow-md transition-all">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        {{ $tickets->links() }}
                    </div>
                    @else
                        {{ __("No tickets available") }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
