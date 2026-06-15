<div class="space-y-5">
    @if (! $modal)
        <div>
            <h1 class="text-2xl font-semibold text-zinc-950 dark:text-white">
                {{ $category ? 'Editar categoria' : 'Nova categoria' }}
            </h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Defina o nome e o tipo da categoria.</p>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-5">
        <div>
            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nome</label>
            <input
                type="text"
                wire:model.defer="name"
                placeholder="Ex: Alimentação, salário, mercado..."
                class="mt-1 h-11 w-full rounded-2xl border border-zinc-200 bg-white px-4 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white"
            />
            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo</span>
            <div class="mt-2 grid grid-cols-2 rounded-2xl border border-zinc-200 bg-zinc-50 p-1 dark:border-zinc-800 dark:bg-zinc-950">
                <button
                    type="button"
                    wire:click="$set('type', 'expense')"
                    @disabled($category)
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-60 {{ $type === 'expense' ? 'bg-rose-600 text-white shadow-sm' : 'text-zinc-600 hover:bg-white dark:text-zinc-300 dark:hover:bg-zinc-900' }}"
                >
                    Despesa
                </button>
                <button
                    type="button"
                    wire:click="$set('type', 'income')"
                    @disabled($category)
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-60 {{ $type === 'income' ? 'bg-emerald-600 text-white shadow-sm' : 'text-zinc-600 hover:bg-white dark:text-zinc-300 dark:hover:bg-zinc-900' }}"
                >
                    Receita
                </button>
            </div>
            @error('type') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end gap-3 rounded-2xl border border-zinc-200 bg-white px-4 py-4 shadow-sm dark:border-zinc-800">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex h-11 min-w-32 items-center justify-center rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="save">Salvar</span>
                <span wire:loading wire:target="save">Salvando...</span>
            </button>
        </div>
    </form>
</div>
