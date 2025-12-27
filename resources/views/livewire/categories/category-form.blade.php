<div class="space-y-6">

    {{-- Cabeçalho (opcional fora do modal) --}}
    <div class="hidden sm:block">
        <h1 class="text-2xl font-bold">
            {{ $category ? 'Editar categoria' : 'Nova categoria' }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Defina o nome e o tipo da categoria
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
                placeholder="Ex: Alimentação, Salário…"
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900"
            >
            @error('name')
                <p class="mt-1 text-sm text-rose-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Tipo --}}
        <div>
            <label class="text-sm font-medium">
                Tipo
            </label>
            <select
                wire:model.defer="type"
                @if($category) disabled @endif
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900
                       disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <option value="expense">Despesa</option>
                <option value="income">Receita</option>
            </select>
        </div>

        {{-- Ações --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t dark:border-zinc-800">

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
