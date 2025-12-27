<div class="space-y-6">

    {{-- Alertas --}}
    @if (session()->has('success'))
        <div class="rounded-lg bg-emerald-100 text-emerald-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="rounded-lg bg-yellow-100 text-yellow-800 px-4 py-3">
            {{ session('warning') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-rose-100 text-rose-800 px-4 py-3">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Tipo --}}
        <div class="md:col-span-2">
            <label class="text-sm font-medium">Tipo</label>
            <select
                wire:model.live="type"
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900
                       focus:ring-2 focus:ring-indigo-500"
            >
                <option value="">Selecione o tipo</option>
                <option value="expense">Despesa</option>
                <option value="income">Receita</option>
            </select>
        </div>

        {{-- Descrição --}}
        <div class="md:col-span-2">
            <label class="text-sm font-medium">Descrição</label>
            <input
                type="text"
                wire:model.defer="description"
                placeholder="Ex: Aluguel, Salário, Mercado..."
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900"
            >
        </div>

        {{-- Valor --}}
        <div>
            <label class="text-sm font-medium">Valor</label>
            <input
                type="number"
                step="0.01"
                wire:model.defer="total_value"
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900"
            >
        </div>

        {{-- Data --}}
        <div>
            <label class="text-sm font-medium">Data</label>
            <input
                type="date"
                wire:model.defer="start_date"
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900"
            >
        </div>

        {{-- Categoria --}}
        <div class="md:col-span-2">
            <label class="text-sm font-medium">Categoria</label>
            <select
                wire:model.defer="category_id"
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900"
            >
                <option value="">Selecione uma categoria</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Forma de pagamento --}}
        <div class="md:col-span-2">
            <label class="text-sm font-medium">Forma de pagamento</label>
            <select
                wire:model.live="credit_card_id"
                class="mt-1 w-full rounded-lg border p-2.5
                       bg-white dark:bg-zinc-900"
            >
                <option value="">Conta bancária</option>
                @foreach ($cards as $card)
                    <option value="{{ $card->id }}">
                        {{ $card->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Conta OU Parcelas --}}
        @if (!$credit_card_id)
            <div class="md:col-span-2">
                <label class="text-sm font-medium">Conta bancária</label>
                <select
                    wire:model.defer="account_id"
                    class="mt-1 w-full rounded-lg border p-2.5
                           bg-white dark:bg-zinc-900"
                >
                    <option value="">Selecione uma conta</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="md:col-span-2">
                <label class="text-sm font-medium">Parcelas</label>
                <input
                    type="number"
                    min="1"
                    wire:model.defer="installments"
                    class="mt-1 w-full rounded-lg border p-2.5
                           bg-white dark:bg-zinc-900"
                >
            </div>
        @endif

        {{-- Fixa --}}
        @if ($type)
            <div class="md:col-span-2 flex items-center gap-3 pt-1">
                <input
                    type="checkbox"
                    id="is_fixed"
                    wire:model.defer="is_fixed"
                    class="rounded border-gray-300"
                >
                <label for="is_fixed" class="text-sm">
                    {{ $type === 'expense' ? 'Despesa fixa' : 'Receita fixa' }}
                </label>
            </div>
        @endif
    </div>

    {{-- Ações --}}
    <div class="flex items-center justify-end gap-3 pt-4 border-t dark:border-zinc-800">
        <button
            type="button"
            wire:click="save"
            wire:loading.attr="disabled"
            class="px-5 py-2 rounded-lg
                   bg-indigo-600 text-white
                   hover:bg-indigo-500 transition"
        >
            Salvar
        </button>
    </div>

</div>
