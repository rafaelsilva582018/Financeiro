<div class="space-y-5">
    @if (! $modal)
        <div>
            <h1 class="text-2xl font-semibold text-zinc-950 dark:text-white">{{ $creditCard ? 'Editar cartão' : 'Novo cartão' }}</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Informe limite, fechamento e vencimento da fatura.</p>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-5">
        <div>
            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nome</label>
            <input
                type="text"
                wire:model.defer="name"
                placeholder="Ex: Nubank, Itaú, Inter..."
                class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white"
            />
            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div x-data="moneyInput(@js($limit), (value) => $wire.set('limit', value, false))">
            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Limite</label>
            <input
                type="text"
                inputmode="numeric"
                x-model="display"
                x-on:input="update($event.target.value)"
                x-on:focus="$event.target.select()"
                placeholder="R$ 0,00"
                class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white"
            />
            @error('limit') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Dia de fechamento</label>
                <input
                    type="number"
                    min="1"
                    max="28"
                    wire:model.defer="closing_day"
                    class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white"
                />
                @error('closing_day') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Dia de vencimento</label>
                <input
                    type="number"
                    min="1"
                    max="28"
                    wire:model.defer="due_day"
                    class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white"
                />
                @error('due_day') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-zinc-200 pt-5 dark:border-zinc-800">
            @if (! $modal)
                <a href="{{ route('credit-cards.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-zinc-200 px-5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-900">Cancelar</a>
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