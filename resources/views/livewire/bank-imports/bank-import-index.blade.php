<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="flex items-start gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 shadow-sm dark:bg-indigo-950 dark:text-indigo-300">
                <x-ui.icon name="receipt" class="h-6 w-6" />
            </span>
            <div>
                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Banco</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">Pendências bancárias</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Revise Pix, compras e entradas do banco antes de lançar no financeiro.</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-1 border-b border-zinc-200 pb-4 dark:border-zinc-800">
            <h2 class="text-base font-semibold text-zinc-950 dark:text-white">Adicionar movimentação</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Use isto para registrar manualmente uma movimentação vista no banco.</p>
        </div>

        <form wire:submit="createImport" class="mt-5 grid gap-4 lg:grid-cols-[0.8fr_1.4fr_0.7fr_0.7fr_1fr_auto] lg:items-end">
            <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                Tipo
                <select wire:model="type" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                    <option value="expense">Saída</option>
                    <option value="income">Entrada</option>
                </select>
            </label>

            <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                Descrição do banco
                <input type="text" wire:model="description" placeholder="PIX MERCADO, INTERNET, SALÁRIO..." class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                @error('description') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                Valor
                <input type="number" step="0.01" min="0.01" wire:model="amount" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                @error('amount') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                Data
                <input type="date" wire:model="occurred_at" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                @error('occurred_at') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
            </label>

            <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                Conta
                <select wire:model="account_id" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                    <option value="">Selecionar depois</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach
                </select>
            </label>

            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                <x-ui.icon name="plus" class="h-4 w-4" />
                Adicionar
            </button>
        </form>
    </section>

    <div class="grid gap-6 xl:grid-cols-[1fr_0.35fr]">
        <section class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-1 border-b border-zinc-200 p-5 dark:border-zinc-800">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-white">Movimentações para revisar</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Vincule com uma despesa existente ou crie uma nova transação.</p>
            </div>

            <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($imports as $import)
                    @php
                        $isPending = $import->status === 'pending';
                        $isIncome = $import->type === 'income';
                        $importCategories = $categories->where('type', $import->type);
                        $entryOptions = $openEntries->filter(fn ($entry) => $entry->transaction?->type === $import->type);
                        $suggestedCategoryId = $import->category_id ?? $import->suggested_category_id;
                        $selectedCategory = $categorySelection[$import->id] ?? $suggestedCategoryId;
                        $selectedAccount = $accountSelection[$import->id] ?? $import->account_id;
                    @endphp

                    <article class="p-5">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $isIncome ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                                        {{ $isIncome ? 'Entrada' : 'Saída' }}
                                    </span>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $isPending ? 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }}">
                                        {{ ['pending' => 'Pendente', 'linked' => 'Vinculada', 'created' => 'Criada', 'ignored' => 'Ignorada'][$import->status] ?? $import->status }}
                                    </span>
                                    @if ($import->suggestedCategory || $import->suggestedTransaction)
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                            Sugestão encontrada
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-3 text-base font-semibold text-zinc-950 dark:text-white">{{ $import->description }}</h3>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $import->occurred_at->format('d/m/Y') }} · {{ $import->account?->name ?? 'Conta não definida' }}
                                </p>

                                @if ($import->suggestedCategory || $import->suggestedTransaction)
                                    <p class="mt-2 text-sm text-indigo-600 dark:text-indigo-300">
                                        Sugestão:
                                        @if ($import->suggestedCategory)
                                            categoria {{ $import->suggestedCategory->name }}
                                        @endif
                                        @if ($import->suggestedTransaction)
                                            {{ $import->suggestedCategory ? 'e' : '' }} vínculo com {{ $import->suggestedTransaction->description }}
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <p class="text-right text-xl font-semibold {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $isIncome ? '+' : '-' }} R$ {{ number_format($import->amount, 2, ',', '.') }}
                            </p>
                        </div>

                        @if ($isPending)
                            <div class="mt-5 grid gap-3 lg:grid-cols-3">
                                <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                    Categoria
                                    <select wire:model="categorySelection.{{ $import->id }}" class="mt-1 h-10 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                                        <option value="">Escolher categoria</option>
                                        @foreach ($importCategories as $category)
                                            <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("categorySelection.$import->id") <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </label>

                                <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                    Lançamento existente
                                    <select wire:model="entrySelection.{{ $import->id }}" class="mt-1 h-10 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                                        <option value="">Escolher lançamento</option>
                                        @foreach ($entryOptions as $entry)
                                            <option value="{{ $entry->id }}">
                                                {{ $entry->reference_date->format('d/m/Y') }} · {{ $entry->transaction?->description }} · R$ {{ number_format($entry->value, 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("entrySelection.$import->id") <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </label>

                                <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                    Conta para nova transação
                                    <select wire:model="accountSelection.{{ $import->id }}" class="mt-1 h-10 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                                        <option value="">Escolher conta</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" @selected((string) $selectedAccount === (string) $account->id)>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("accountSelection.$import->id") <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </label>
                            </div>

                            @if (! $isIncome)
                                <div class="mt-4 rounded-lg border border-indigo-100 bg-indigo-50/70 p-4 dark:border-indigo-900/50 dark:bg-indigo-950/20">
                                    <div class="flex flex-col gap-1">
                                        <h4 class="text-sm font-semibold text-zinc-950 dark:text-white">Pagamento de fatura do cartão</h4>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Use quando essa saída do banco for o pagamento da fatura. Ele fecha todas as compras pendentes do cartão no mês escolhido.</p>
                                    </div>

                                    <div class="mt-4 grid gap-3 lg:grid-cols-[1fr_0.7fr_auto] lg:items-end">
                                        <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                            Cartão
                                            <select wire:model="invoiceCardSelection.{{ $import->id }}" class="mt-1 h-10 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                                                <option value="">Escolher cartão</option>
                                                @foreach ($cards as $card)
                                                    <option value="{{ $card->id }}">{{ $card->name }}</option>
                                                @endforeach
                                            </select>
                                            @error("invoiceCardSelection.$import->id") <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                        </label>

                                        <label class="block text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                            Mês da fatura
                                            <input type="month" wire:model="invoiceMonthSelection.{{ $import->id }}" value="{{ $import->occurred_at->format('Y-m') }}" class="mt-1 h-10 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-950 dark:text-white">
                                            @error("invoiceMonthSelection.$import->id") <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                        </label>

                                        <button type="button" wire:click="closeCardInvoice({{ $import->id }})" class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white transition hover:bg-emerald-500">
                                            Fechar fatura
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <label class="inline-flex items-center gap-2 text-sm font-medium text-zinc-600 dark:text-zinc-300">
                                    <input type="checkbox" wire:model="saveRuleSelection.{{ $import->id }}" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-950">
                                    Usar esta escolha nas próximas parecidas
                                </label>

                                <div class="flex flex-wrap gap-2">
                                    @if ($import->suggestedCategory || $import->suggestedTransaction)
                                        <button type="button" wire:click="applySuggestion({{ $import->id }})" class="inline-flex h-10 items-center justify-center rounded-lg border border-indigo-200 px-4 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-900 dark:text-indigo-300 dark:hover:bg-indigo-950">
                                            Aplicar sugestão
                                        </button>
                                    @endif
                                    <button type="button" wire:click="confirmCategory({{ $import->id }})" class="inline-flex h-10 items-center justify-center rounded-lg border border-zinc-200 px-4 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                        Categorizar
                                    </button>
                                    <button type="button" wire:click="linkToEntry({{ $import->id }})" class="inline-flex h-10 items-center justify-center rounded-lg border border-emerald-200 px-4 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 dark:border-emerald-900 dark:text-emerald-300 dark:hover:bg-emerald-950">
                                        Vincular
                                    </button>
                                    <button type="button" wire:click="createTransactionFromImport({{ $import->id }})" class="inline-flex h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                        Criar transação
                                    </button>
                                    <button type="button" wire:click="ignoreImport({{ $import->id }})" class="inline-flex h-10 items-center justify-center rounded-lg px-4 text-sm font-semibold text-zinc-500 transition hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800">
                                        Ignorar
                                    </button>
                                </div>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="px-5 py-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300">
                            <x-ui.icon name="receipt" class="h-6 w-6" />
                        </div>
                        <p class="mt-3 font-semibold text-zinc-900 dark:text-white">Nenhuma pendência bancária</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Movimentações registradas vão aparecer aqui para revisão.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-base font-semibold text-zinc-950 dark:text-white">Regras ativas</h2>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">O sistema sugere categoria ou vínculo quando a descrição do banco repetir.</p>

            <div class="mt-5 space-y-3">
                @forelse ($rules as $rule)
                    <div class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                        <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ $rule->keyword }}</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $rule->category?->name ?? 'Sem categoria' }}
                            @if ($rule->transaction)
                                · {{ $rule->transaction->description }}
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-zinc-300 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        Nenhuma regra criada ainda.
                    </p>
                @endforelse
            </div>
        </aside>
    </div>
</div>
