<div class="space-y-5">
    @if (! $modal)
        <div>
            <h1 class="text-2xl font-semibold text-zinc-950 dark:text-white">{{ $account ? 'Editar conta' : 'Nova conta' }}</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Cadastre carteiras e contas bancárias para organizar seus saldos.</p>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-5">
        <div>
            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nome</label>
            <input
                type="text"
                wire:model.defer="name"
                placeholder="Ex: Conta corrente, Carteira, Poupança..."
                class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white"
            />
            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo</span>
            <div class="mt-2 grid grid-cols-2 rounded-lg border border-zinc-200 bg-zinc-50 p-1 dark:border-zinc-800 dark:bg-zinc-950">
                <button type="button" wire:click="$set('type', 'wallet')" class="rounded-md px-4 py-2 text-sm font-semibold transition {{ $type === 'wallet' ? 'bg-indigo-600 text-white shadow-sm' : 'text-zinc-600 hover:bg-white dark:text-zinc-300 dark:hover:bg-zinc-900' }}">Carteira</button>
                <button type="button" wire:click="$set('type', 'bank')" class="rounded-md px-4 py-2 text-sm font-semibold transition {{ $type === 'bank' ? 'bg-indigo-600 text-white shadow-sm' : 'text-zinc-600 hover:bg-white dark:text-zinc-300 dark:hover:bg-zinc-900' }}">Banco</button>
            </div>
            @error('type') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div x-data="moneyInput(@js($initial_balance), (value) => $wire.set('initial_balance', value, false))">
            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Saldo inicial</label>
            <input
                type="text"
                inputmode="numeric"
                x-model="display"
                x-on:input="update($event.target.value)"
                x-on:focus="$event.target.select()"
                placeholder="R$ 0,00"
                class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white"
            />
            @error('initial_balance') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-zinc-200 pt-5 dark:border-zinc-800">
            @if (! $modal)
                <a href="{{ route('accounts.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-zinc-200 px-5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-900">Cancelar</a>
            @endif
            <button type="submit" wire:loading.attr="disabled" class="inline-flex h-11 min-w-32 items-center justify-center rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-60">
                <span wire:loading.remove wire:target="save">Salvar</span>
                <span wire:loading wire:target="save">Salvando...</span>
            </button>
        </div>
    </form>

    <script>
        window.moneyInput ??= (initial, setter) => ({
            display: '',
            init() {
                this.display = this.format(Number(initial) || 0);
            },
            format(value) {
                return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
            },
            update(value) {
                const digits = String(value || '').replace(/\D/g, '').replace(/^0+(?=\d)/, '');
                const amount = Number(digits || 0) / 100;
                this.display = this.format(amount);
                setter(amount);
            }
        });
    </script>
</div>