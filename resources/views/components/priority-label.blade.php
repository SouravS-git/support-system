@props([
    'priority' => 'low',
    'class' => match ($priority){
        'low' => 'bg-green-500/10 text-green-500 border-green-500/20',
        'medium' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
        'high' => 'bg-red-500/10 text-red-500 border-red-500/20'
    }
])
<span class="inline-block rounded-full border px-2 py-1 text-xs font-medium {{ $class }}">
    {{ $slot }}
</span>
