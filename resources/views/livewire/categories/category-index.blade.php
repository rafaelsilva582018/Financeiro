<div
    class="rounded-xl border
           bg-white dark:bg-zinc-900
           p-6 shadow-sm"
>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">
            Categorias
        </h1>

        <a
            href="{{ route('categories.create') }}"
            class="inline-flex items-center gap-2
                   rounded-lg bg-indigo-600 px-4 py-2
                   text-white font-medium
                   hover:bg-indigo-500 transition"
        >
            🏷️ Nova categoria
        </a>
    </div>

    {{-- Tabela --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500 dark:text-gray-400">
                    <th class="pb-3 font-medium">Nome</th>
                    <th class="pb-3 font-medium">Tipo</th>
                    <th class="pb-3 w-32 text-right"></th>
                </tr>
            </thead>

            <tbody class="divide-y dark:divide-zinc-800">
                @forelse ($categories as $category)
                    <tr
                        class="hover:bg-gray-50 dark:hover:bg-zinc-800
                               transition"
                    >
                        {{-- Nome --}}
                        <td class="py-3">
                            <p class="font-medium">
                                {{ $category->name }}
                            </p>
                        </td>

                        {{-- Tipo --}}
                        <td class="py-3">
                            <span
                                class="inline-flex items-center gap-1
                                       rounded-full px-2 py-0.5
                                       text-xs font-medium
                                       {{ $category->type === 'income'
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                            : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}"
                            >
                                {{ $category->type === 'income'
                                    ? 'Receita'
                                    : 'Despesa' }}
                            </span>
                        </td>

                        {{-- Ações --}}
                        <td class="py-3">
                            <div class="flex items-center justify-end gap-3">
                                <a
                                    href="{{ route('categories.edit', $category) }}"
                                    class="text-sm font-medium
                                           text-indigo-600 hover:underline"
                                >
                                    Editar
                                </a>

                                <button
                                    wire:click="delete({{ $category->id }})"
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
                            colspan="3"
                            class="py-8 text-center text-gray-500"
                        >
                            Nenhuma categoria cadastrada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
