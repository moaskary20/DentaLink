<div class="fi-language-switcher flex items-center gap-1 rounded-lg border border-gray-200 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-gray-900">
    @foreach ($locales as $code => $label)
        <button
            type="button"
            wire:click="switch('{{ $code }}')"
            @class([
                'rounded-md px-2.5 py-1 text-xs font-semibold transition',
                'bg-primary-500 text-white' => $current === $code,
                'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' => $current !== $code,
            ])
        >
            {{ strtoupper($code) }}
        </button>
    @endforeach
</div>
