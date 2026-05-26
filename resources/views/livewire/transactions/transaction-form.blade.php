<div class="space-y-5">
    @if (session()->has('success') && ! $modal)
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300">
            Revise os campos destacados antes de salvar.
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-5">
        <section class="rounded-xl border border-zinc-200 bg-zinc-50/60 p-4 dark:border-zinc-800 dark:bg-zinc-950/50">
            <div class="mb-4 flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><x-ui.icon name="receipt" class="h-4 w-4" /></span>
                <div><h3 class="text-sm font-semibold text-zinc-950 dark:text-white">Dados principais</h3><p class="text-xs text-zinc-500 dark:text-zinc-400">Tipo, descrição, valor e data da transação.</p></div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo</span>
                    <div class="mt-2 grid grid-cols-2 rounded-lg border border-zinc-200 bg-white p-1 dark:border-zinc-800 dark:bg-zinc-900">
                        <button type="button" wire:click="$set('type', 'expense')" class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-semibold transition {{ $type === 'expense' ? 'bg-rose-600 text-white shadow-sm' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"><x-ui.icon name="arrow-down" class="h-4 w-4" />Despesa</button>
                        <button type="button" wire:click="$set('type', 'income')" class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-semibold transition {{ $type === 'income' ? 'bg-emerald-600 text-white shadow-sm' : 'text-zinc-600 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"><x-ui.icon name="arrow-up" class="h-4 w-4" />Receita</button>
                    </div>
                    @error('type') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descrição</label>
                    <input type="text" wire:model.defer="description" placeholder="Ex: Aluguel, salário, mercado..." class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white" />
                    @error('description') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                @if (! $credit_card_id || $card_value_mode === 'total')
                <div x-data="{ display: '', init() { this.display = this.format(Number(@js($total_value)) || 0); }, onlyDigits(value) { return String(value || '').replace(/\D/g, '').replace(/^0+(?=\d)/, ''); }, format(value) { return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0); }, update(value) { const digits = this.onlyDigits(value); const amount = (Number(digits || 0) / 100); this.display = this.format(amount); $wire.set('total_value', amount, false); } }">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $credit_card_id ? 'Valor total da compra' : 'Valor' }}</label>
                    <input type="text" inputmode="numeric" x-model="display" x-on:input="update($event.target.value)" x-on:focus="$event.target.select()" placeholder="R$ 0,00" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white" />
                    @error('total_value') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                @endif

                <div>
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Data</label>
                    <input type="date" wire:model.defer="start_date" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white" />
                    @error('start_date') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-4 flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><x-ui.icon name="tags" class="h-4 w-4" /></span>
                <div><h3 class="text-sm font-semibold text-zinc-950 dark:text-white">Classificação e pagamento</h3><p class="text-xs text-zinc-500 dark:text-zinc-400">Escolha categoria, conta ou cartão.</p></div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Categoria</label>
                    <select wire:model.defer="category_id" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                        <option value="">Selecione uma categoria</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Forma de pagamento</label>
                    <select wire:model.live="credit_card_id" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                        <option value="">Conta bancária</option>
                        @foreach ($cards as $card)
                            <option value="{{ $card->id }}">{{ $card->name }}</option>
                        @endforeach
                    </select>
                    @error('credit_card_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                @if (! $credit_card_id)
                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Conta bancária</label>
                        <select wire:model.defer="account_id" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                            <option value="">Selecione uma conta</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                        @error('account_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                @else
                    <div class="md:col-span-2">
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Informar valor do cartão</span>
                        <div class="mt-2 grid grid-cols-2 rounded-lg border border-zinc-200 bg-zinc-50 p-1 dark:border-zinc-800 dark:bg-zinc-950">
                            <button type="button" wire:click="$set('card_value_mode', 'total')" class="rounded-md px-3 py-2 text-sm font-semibold transition {{ $card_value_mode === 'total' ? 'bg-white text-zinc-950 shadow-sm dark:bg-zinc-900 dark:text-white' : 'text-zinc-600 hover:bg-white/70 dark:text-zinc-300 dark:hover:bg-zinc-900/70' }}">
                                Total da compra
                            </button>
                            <button type="button" wire:click="$set('card_value_mode', 'installment')" class="rounded-md px-3 py-2 text-sm font-semibold transition {{ $card_value_mode === 'installment' ? 'bg-white text-zinc-950 shadow-sm dark:bg-zinc-900 dark:text-white' : 'text-zinc-600 hover:bg-white/70 dark:text-zinc-300 dark:hover:bg-zinc-900/70' }}">
                                Valor da parcela
                            </button>
                        </div>
                        @error('card_value_mode') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Parcelas</label>
                        <input type="number" min="1" wire:model.defer="installments" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white" />
                        @error('installments') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    @if ($card_value_mode === 'installment')
                        <div x-data="{ display: '', init() { this.display = this.format(Number(@js($installment_value)) || 0); }, onlyDigits(value) { return String(value || '').replace(/\D/g, '').replace(/^0+(?=\d)/, ''); }, format(value) { return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0); }, update(value) { const digits = this.onlyDigits(value); const amount = (Number(digits || 0) / 100); this.display = this.format(amount); $wire.set('installment_value', amount, false); } }">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Valor da parcela</label>
                            <input type="text" inputmode="numeric" x-model="display" x-on:input="update($event.target.value)" x-on:focus="$event.target.select()" placeholder="R$ 0,00" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white" />
                            @error('installment_value') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-700 dark:border-indigo-900/60 dark:bg-indigo-950/40 dark:text-indigo-300">
                            Total calculado: R$ {{ number_format(($installment_value ?: 0) * max((int) ($installments ?: 1), 1), 2, ',', '.') }}
                        </div>
                    @endif
                @endif

                @if ($type)
                    <label class="md:col-span-2 flex items-center justify-between rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
                        <span><span class="block text-sm font-semibold text-zinc-800 dark:text-zinc-100">{{ $type === 'expense' ? 'Despesa fixa' : 'Receita fixa' }}</span><span class="block text-xs text-zinc-500 dark:text-zinc-400">Repete automaticamente nos próximos meses.</span></span>
                        <input type="checkbox" wire:model.defer="is_fixed" class="h-5 w-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" />
                    </label>
                @endif
            </div>
        </section>

        <div class="sticky bottom-0 -mx-5 flex items-center justify-end gap-3 border-t border-zinc-200 bg-white/95 px-5 py-4 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/95 sm:-mx-6 sm:px-6">
            @if ($modal)
                <button type="button" x-on:click="$dispatch('close-resource-modal'); $dispatch('close-dashboard-modal')" class="inline-flex h-11 items-center justify-center rounded-lg border border-zinc-200 px-5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-800">Cancelar</button>
            @endif
            <button type="submit" wire:loading.attr="disabled" class="inline-flex h-11 min-w-36 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-60">
                <x-ui.icon name="check" class="h-4 w-4" />
                <span wire:loading.remove wire:target="save">Salvar</span>
                <span wire:loading wire:target="save">Salvando...</span>
            </button>
        </div>
    </form>
</div>

