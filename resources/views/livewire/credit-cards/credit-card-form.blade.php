<div
    class="max-w-xl mx-auto
           rounded-xl border
           bg-white dark:bg-zinc-900
           p-6 shadow-sm space-y-6"
>

    {{-- Cabeçalho --}}
    <div>
        <h1 class="text-2xl font-bold">
            {{ $creditCard ? 'Editar cartão' : 'Novo cartão' }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Informe os dados do cartão de crédito
        </p>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">

        {{-- Nome --}}
        <div>
            <label class="text-sm font-medium">
                Nome
            </label>
            <input
                type="text"
                wire:model.defer="name"
                placeholder="Ex: Nubank, Itaú, Inter…"
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900"
            >
        </div>

        {{-- Limite --}}
        <div>
            <label class="text-sm font-medium">
                Limite
            </label>
            <input
                type="number"
                step="0.01"
                wire:model.defer="limit"
                placeholder="0,00"
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900"
            >
        </div>

        {{-- Fechamento / Vencimento --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label class="text-sm font-medium">
                    Dia de fechamento
                </label>
                <input
                    type="number"
                    min="1"
                    max="28"
                    wire:model.defer="closing_day"
                    class="mt-1 w-full rounded-lg border p-2.5
                           bg-white dark:bg-zinc-900"
                >
            </div>

            <div>
                <label class="text-sm font-medium">
                    Dia de vencimento
                </label>
                <input
                    type="number"
                    min="1"
                    max="28"
                    wire:model.defer="due_day"
                    class="mt-1 w-full rounded-lg border p-2.5
                           bg-white dark:bg-zinc-900"
                >
            </div>

        </div>

        {{-- Ações --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t dark:border-zinc-800">

            <a
                href="{{ route('credit-cards.index') }}"
                class="px-4 py-2 rounded-lg border
                       hover:bg-gray-50 dark:hover:bg-zinc-800 transition"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="px-5 py-2 rounded-lg
                       bg-indigo-600 text-white
                       hover:bg-indigo-500 transition"
            >
                Salvar
            </button>
        </div>

    </form>
</div>
