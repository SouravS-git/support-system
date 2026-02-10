@props([
    'status' => 'open',
    'class' => match($status) {
        'open'                 => 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]',
        'in_progress'          => 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]',
        'waiting_for_customer' => 'bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.6)]',
        'resolved'             => 'bg-slate-400 shadow-[0_0_8px_rgba(148,163,184,0.6)]',
        default                => '',
    }
])
<div class="flex items-center gap-2">
    <div class="w-1.5 h-1.5 rounded-full {{ $class }}"></div>
    <span class="text-[10px] font-bold tracking-[0.1em] text-slate-400 uppercase">{{ $slot }}</span>
</div>

