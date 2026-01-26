<div
    class="rounded-xl border
           bg-white dark:bg-zinc-900
           p-6 shadow-sm"
>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">
            Transações
        </h1>

        <a
            href="{{ route('transactions.create') }}"
            class="inline-flex items-center gap-2
                   rounded-lg bg-indigo-600 px-4 py-2
                   text-white font-medium
                   hover:bg-indigo-500 transition"
        >
            ➕ Nova transação
        </a>
    </div>

    {{-- Tabela --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500 dark:text-gray-400">
                    <th class="pb-3 font-medium">Descrição</th>
                    <th class="pb-3 font-medium">Tipo</th>
                    <th class="pb-3 font-medium text-right">Valor</th>
                    <th class="pb-3 font-medium text-center">Data</th>
                    <th class="pb-3 font-medium text-right">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y dark:divide-zinc-800">
                @forelse ($transactions as $transaction)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                        {{-- Descrição --}}
                        <td class="py-3">
                            <p class="font-medium">
                                {{ $transaction->description }}
                            </p>
                        </td>

                        {{-- Tipo --}}
                        <td class="py-3">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium
                                       {{ $transaction->type === 'income'
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                            : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}"
                            >
                                {{ $transaction->type === 'income' ? 'Receita' : 'Despesa' }}
                            </span>
                        </td>

                        {{-- Valor --}}
                        <td
                            class="py-3 text-right font-semibold
                                   {{ $transaction->type === 'income'
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : 'text-rose-600 dark:text-rose-400' }}"
                        >
                            R$ {{ number_format($transaction->total_value, 2, ',', '.') }}
                        </td>

                        {{-- Data --}}
                        <td class="py-3 text-center text-gray-500 dark:text-gray-400">
                            {{ $transaction->start_date->format('d/m/Y') }}
                        </td>

                        {{-- Ações --}}
                        <td class="py-3 text-right">
                            <div class="flex justify-end gap-2">
                                {{-- EDITAR --}}
                                <a
                                    href="{{ route('transactions.edit', $transaction->id) }}"
                                    class="px-3 py-1 rounded-lg text-xs font-medium
                                           bg-gray-100 hover:bg-gray-200
                                           dark:bg-zinc-800 dark:hover:bg-zinc-700"
                                >
                                    ✏️ Editar
                                </a>

                                {{-- DELETAR --}}
                                <button
                                    type="button"
                                    wire:click="delete({{ $transaction->id }})"
                                    wire:confirm="Tem certeza que deseja excluir?"
                                    class="px-3 py-1 rounded-lg text-xs font-medium
                                           bg-rose-600 text-white hover:bg-rose-500"
                                >
                                    🗑️ Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            Nenhuma transação encontrada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
