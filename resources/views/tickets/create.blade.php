<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Ticket') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('tickets.store') }}" method="POST">
                        @csrf
                        <div class="space-y-12">
                            <div class="border-b border-gray-900/10 pb-12">
                                <h2 class="text-base/7 font-semibold text-gray-900">Ticket Details</h2>
                                <p class="mt-1 text-sm/6 text-gray-600">These information will be shared with our agent to provide further solutions.</p>

                                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="col-span-full">
                                        <label for="subject" class="block text-sm/6 font-medium text-gray-900">Subject*</label>
                                        <div class="mt-2">
                                            <input id="subject" type="text" name="subject" value="{{ old('subject') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                                            @error('subject')
                                                <x-input-error :messages="$message"></x-input-error>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-span-full">
                                        <label for="about" class="block text-sm/6 font-medium text-gray-900">Description*</label>
                                        <div class="mt-2">
                                            <textarea id="description" name="description" rows="5" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{ old('description') }}</textarea>
                                            @error('description')
                                                <x-input-error :messages="$message"></x-input-error>
                                            @enderror
                                        </div>
                                        <p class="mt-3 text-sm/6 text-gray-600">Write a few sentences about your issue.</p>
                                    </div>
                                </div>

                                <div class="mt-10 space-y-10">
                                    <fieldset>
                                        <legend class="text-sm/6 font-semibold text-gray-900">Select Priority*</legend>
                                        <p class="mt-1 text-sm/6 text-gray-600">Resolution time depends on the assigned priority.</p>
                                        <div class="mt-6 space-y-6">
                                            <div class="flex items-center gap-x-3">
                                                @foreach (\App\TicketPriority::cases() as $ticketPriority)
                                                    <div class="flex items-center gap-x-3">
                                                        <input type="radio" name="priority" value="{{ $ticketPriority->value }}" class="relative size-4 appearance-none rounded-full border border-gray-300 bg-white before:absolute before:inset-1 before:rounded-full before:bg-white not-checked:before:hidden checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 forced-colors:appearance-auto forced-colors:before:hidden" />
                                                        <label for="{{ $ticketPriority->value }}" class="block text-sm/6 font-medium text-gray-900">{{ $ticketPriority->label() }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('priority')
                                                <x-input-error :messages="$message"></x-input-error>
                                            @enderror
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <button type="reset" class="text-sm/6 font-semibold text-gray-900">Cancel</button>
                            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
