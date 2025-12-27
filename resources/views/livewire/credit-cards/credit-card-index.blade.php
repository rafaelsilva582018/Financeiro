<div
    class="rounded-xl border
           bg-white dark:bg-zinc-900
           p-6 shadow-sm"
>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">
            Cartões de crédito
        </h1>

        <a
            href="{{ route('credit-cards.create') }}"
            class="inline-flex items-center gap-2
                   rounded-lg bg-indigo-600 px-4 py-2
                   text-white font-medium
                   hover:bg-indigo-500 transition"
        >
            💳 Novo cartão
        </a>
    </div>

    {{-- Tabela --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500 dark:text-gray-400">
                    <th class="pb-3 font-medium">Nome</th>
                    <th class="pb-3 font-medium text-right">Limite</th>
                    <th class="pb-3 font-medium text-center">Fechamento</th>
                    <th class="pb-3 font-medium text-center">Vencimento</th>
                    <th class="pb-3 w-32 text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y dark:divide-zinc-800">
                @forelse ($cards as $card)
                    <tr
                        class="hover:bg-gray-50 dark:hover:bg-zinc-800
                               transition"
                    >
                        {{-- Nome --}}
                        <td class="py-3">
                            <p class="font-medium">
                                {{ $card->name }}
                            </p>
                        </td>

                        {{-- Limite --}}
                        <td class="py-3 text-right font-semibold">
                            R$ {{ number_format($card->limit, 2, ',', '.') }}
                        </td>

                        {{-- Fechamento --}}
                        <td class="py-3 text-center text-gray-500 dark:text-gray-400">
                            Dia {{ $card->closing_day }}
                        </td>

                        {{-- Vencimento --}}
                        <td class="py-3 text-center text-gray-500 dark:text-gray-400">
                            Dia {{ $card->due_day }}
                        </td>

                        {{-- Ações --}}
                        <td class="py-3">
                            <div class="flex items-center justify-end gap-3">
                                <a
                                    href="{{ route('credit-cards.edit', $card) }}"
                                    class="text-sm font-medium
                                           text-indigo-600 hover:underline"
                                >
                                    Editar
                                </a>

                                <button
                                    wire:click="delete({{ $card->id }})"
                                    class="text-sm font-medium
                                           text-rose-600 hover:underline"
                                >
                                    Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="5"
                            class="py-8 text-center text-gray-500"
                        >
                            Nenhum cartão cadastrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
