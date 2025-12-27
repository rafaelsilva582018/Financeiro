<div
    class="rounded-xl border
           bg-white dark:bg-zinc-900
           p-6 shadow-sm"
>
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold">
            Lançamentos
        </h1>

        <input
            type="month"
            wire:model.live="month"
            class="rounded-lg border p-2
                   bg-white dark:bg-zinc-900
                   focus:ring-2 focus:ring-indigo-500"
        >
    </div>

    {{-- Loading feedback --}}
    <div
        wire:loading.delay
        wire:target="month"
        class="mb-4 flex items-center gap-2
               text-sm text-gray-500 dark:text-gray-400"
    >
        <svg
            class="h-4 w-4 animate-spin"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            />
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
            />
        </svg>

        Carregando lançamentos…
    </div>

    {{-- Tabela --}}
    <div
        class="overflow-x-auto transition-opacity"
        wire:loading.class="opacity-50"
        wire:target="month"
    >
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500 dark:text-gray-400">
                    <th class="pb-3 font-medium">Descrição</th>
                    <th class="pb-3 font-medium">Tipo</th>
                    <th class="pb-3 font-medium text-right">Valor</th>
                    <th class="pb-3 font-medium text-center">Status</th>
                    <th class="pb-3 w-24"></th>
                </tr>
            </thead>

            <tbody class="divide-y dark:divide-zinc-800">
                @forelse ($entries as $entry)
                    <tr
                        class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition"
                    >
                        {{-- Descrição --}}
                        <td class="py-3 font-medium">
                            {{ $entry->transaction->description }}
                        </td>

                        {{-- Tipo --}}
                        <td class="py-3">
                            <span
                                class="inline-flex rounded-full px-2 py-0.5
                                       text-xs font-medium
                                       {{ $entry->transaction->type === 'income'
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                            : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}"
                            >
                                {{ $entry->transaction->type === 'income'
                                    ? 'Receita'
                                    : 'Despesa' }}
                            </span>
                        </td>

                        {{-- Valor --}}
                        <td
                            class="py-3 text-right font-semibold
                                   {{ $entry->transaction->type === 'income'
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : 'text-rose-600 dark:text-rose-400' }}"
                        >
                            R$ {{ number_format($entry->value, 2, ',', '.') }}
                        </td>

                        {{-- Status --}}
                        <td class="py-3 text-center">
                            <span
                                class="inline-flex rounded-full px-2 py-0.5
                                       text-xs font-medium
                                       {{ $entry->status === 'paid'
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                            : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' }}"
                            >
                                {{ $entry->status === 'paid'
                                    ? 'Pago'
                                    : 'Pendente' }}
                            </span>
                        </td>

                        {{-- Ação --}}
                        <td class="py-3 text-center">
                            <button
                                wire:click="toggleStatus({{ $entry->id }})"
                                wire:loading.attr="disabled"
                                wire:target="month"
                                class="text-sm font-medium
                                       text-indigo-600 hover:underline
                                       disabled:opacity-50
                                       disabled:cursor-not-allowed"
                            >
                                Alternar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            Nenhum lançamento encontrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
