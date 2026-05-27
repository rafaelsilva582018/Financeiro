<div class="space-y-6" x-data="{ modal: null }" x-init="$nextTick(() => window.FinanceCharts?.dashboardFromPage())" x-on:close-dashboard-modal.window="modal = null">
    <script type="application/json" data-dashboard-chart-payload>@json(["monthly" => $monthlyChart, "yearly" => $yearlyChart, "categories" => $categoryChart])</script>
    <div class="grid gap-4 lg:grid-cols-[1fr_auto_1fr] lg:items-end">
        <div class="lg:justify-self-start">
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Visão financeira</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Acompanhe saldo, fluxo do mês, faturas e lançamentos em aberto.
            </p>
        </div>

        <div
            x-data="{
                openMonthPicker() {
                    if (this.$refs.monthPicker?.showPicker) {
                        this.$refs.monthPicker.showPicker();
                        return;
                    }

                    this.$refs.monthPicker?.focus();
                    this.$refs.monthPicker?.click();
                },
            }"
            class="flex flex-col items-center gap-2 lg:justify-self-center"
        >
            <p class="text-xs font-semibold uppercase text-zinc-500 dark:text-zinc-400">Mês de referência</p>
            <div class="inline-flex h-10 items-center overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <a
                    href="{{ route('dashboard', ['month' => $previousMonth]) }}"
                    class="inline-flex h-10 w-10 items-center justify-center text-lg font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800"
                    aria-label="Mês anterior"
                >
                    ‹
                </a>
                <button
                    type="button"
                    x-on:click="openMonthPicker()"
                    class="relative inline-flex h-10 min-w-36 cursor-pointer items-center justify-center px-5 text-sm font-semibold text-zinc-950 transition hover:bg-zinc-50 dark:text-white dark:hover:bg-zinc-800"
                    aria-label="Escolher mês"
                >
                    <span>{{ $selectedMonthLabel }}</span>
                    <input
                        x-ref="monthPicker"
                        type="month"
                        value="{{ $month }}"
                        onchange="if (this.value) window.location.href = '{{ route('dashboard') }}?month=' + this.value"
                        class="pointer-events-none absolute inset-0 h-full w-full cursor-pointer opacity-0"
                        aria-label="Escolher mês"
                    />
                </button>
                <a
                    href="{{ route('dashboard', ['month' => $nextMonth]) }}"
                    class="inline-flex h-10 w-10 items-center justify-center text-lg font-semibold text-zinc-700 transition hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800"
                    aria-label="Próximo mês"
                >
                    ›
                </a>
            </div>
        </div>

        <div class="flex justify-start lg:justify-end">
            <a href="{{ route('transactions.create') }}" wire:navigate class="inline-flex h-12 w-full items-center justify-center rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 sm:w-auto sm:min-w-44 gap-2"><x-ui.icon name="plus" class="h-4 w-4" />Nova transação</a>
        </div>
    </div>

    <div
        wire:loading.delay
        wire:target="month,toggleEntryStatus"
        class="rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-700 dark:border-indigo-900/50 dark:bg-indigo-950/40 dark:text-indigo-300"
    >
        Atualizando os dados do dashboard...
    </div>

    <div wire:loading.class="opacity-60 pointer-events-none" wire:target="month,toggleEntryStatus" class="space-y-6 transition-opacity">
        <div class="grid items-stretch gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex min-h-44 flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium leading-6 text-zinc-500 dark:text-zinc-400">Saldo do mês</p>
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-semibold leading-5 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300"><x-ui.icon name="calendar" class="h-3.5 w-3.5" />{{ $selectedMonthLabel }}</span>
                </div>

                <div class="mt-6">
                    <p class="text-3xl font-semibold leading-none tracking-tight {{ ($summary['final_balance'] ?? 0) >= 0 ? 'text-zinc-950 dark:text-white' : 'text-rose-600 dark:text-rose-400' }}">
                        R$ {{ number_format($summary['final_balance'] ?? 0, 2, ',', '.') }}
                    </p>
                    <p class="mt-4 text-sm leading-5 text-zinc-500 dark:text-zinc-400">
                        Saldo inicial: R$ {{ number_format($summary['initial_balance'] ?? 0, 2, ',', '.') }}
                    </p>
                </div>
            </div>

            <a href="{{ route('reports.income', ['month' => $month]) }}" wire:navigate class="flex min-h-44 flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-emerald-900">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium leading-6 text-zinc-500 dark:text-zinc-400">Receitas pagas</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-300"><x-ui.icon name="arrow-up" class="h-4 w-4" /></span>
                </div>

                <div class="mt-6">
                    <p class="text-3xl font-semibold leading-none tracking-tight text-emerald-600 dark:text-emerald-400">
                        R$ {{ number_format($summary['income'] ?? 0, 2, ',', '.') }}
                    </p>
                    <p class="mt-4 text-sm leading-5 text-zinc-500 dark:text-zinc-400">Ver relatório de receitas.</p>
                </div>
            </a>

            <a href="{{ route('reports.expenses', ['month' => $month]) }}" wire:navigate class="flex min-h-44 flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-rose-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-rose-900">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium leading-6 text-zinc-500 dark:text-zinc-400">Despesas pagas</p>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-300"><x-ui.icon name="arrow-down" class="h-4 w-4" /></span>
                </div>

                <div class="mt-6">
                    <p class="text-3xl font-semibold leading-none tracking-tight text-rose-600 dark:text-rose-400">
                        R$ {{ number_format($summary['expenses'] ?? 0, 2, ',', '.') }}
                    </p>
                    <p class="mt-4 text-sm leading-5 text-zinc-500 dark:text-zinc-400">
                        Ver relatório de despesas.
                    </p>
                </div>
            </a>

            <div class="flex min-h-44 flex-col justify-between rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium leading-6 text-zinc-500 dark:text-zinc-400">Pendências</p>
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold leading-5 text-amber-700 dark:bg-amber-950 dark:text-amber-300"><x-ui.icon name="calendar" class="h-3.5 w-3.5" />
                        {{ $health['pending_count'] ?? 0 }} abertas
                    </span>
                </div>

                <div class="mt-6">
                    <p class="text-3xl font-semibold leading-none tracking-tight text-amber-600 dark:text-amber-400">
                        R$ {{ number_format($health['pending_total'] ?? 0, 2, ',', '.') }}
                    </p>
                    <div class="mt-5 h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <div class="h-2 rounded-full bg-emerald-500" style="width: {{ min($health['payment_progress'] ?? 0, 100) }}%"></div>
                    </div>
                    <p class="mt-3 text-sm leading-5 text-zinc-500 dark:text-zinc-400">
                        {{ $health['payment_progress'] ?? 0 }}% dos lançamentos pagos.
                    </p>
                </div>
            </div>
        </div>
        <div class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
            <section class="h-[430px] overflow-hidden rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-950 dark:text-white">Receitas x despesas no ano</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Valores pagos, mês a mês.</p>
                    </div>
                </div>

                <div wire:ignore class="mt-4 h-[322px] overflow-hidden" data-dashboard-chart="yearly"></div>
            </section>

            <section class="h-[430px] overflow-hidden rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div>
                    <h2 class="text-base font-semibold text-zinc-950 dark:text-white">Composição do mês</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Resumo pago e despesas por categoria.</p>
                </div>

                <div class="mt-4 grid h-[322px] grid-rows-[145px_1fr] gap-3 overflow-hidden">
                    <div wire:ignore class="overflow-hidden" data-dashboard-chart="monthly"></div>
                    <div wire:ignore class="overflow-hidden" data-dashboard-chart="categories"></div>
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="inline-flex items-center gap-2 text-base font-semibold text-zinc-950 dark:text-white"><x-ui.icon name="plus" class="h-4 w-4 text-indigo-500" />Atalhos</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Ações usadas no dia a dia.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                    <a href="{{ route('transactions.create') }}" wire:navigate class="group rounded-lg border border-zinc-200 p-4 text-left transition hover:border-indigo-300 hover:bg-indigo-50 dark:border-zinc-800 dark:hover:border-indigo-800 dark:hover:bg-indigo-950/30"><p class="font-semibold text-zinc-950 dark:text-white">Registrar transação</p><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Receita, despesa, fixo ou parcelado.</p></a>

                    <a href="{{ route('entries.index') }}" class="group rounded-lg border border-zinc-200 p-4 transition hover:border-indigo-300 hover:bg-indigo-50 dark:border-zinc-800 dark:hover:border-indigo-800 dark:hover:bg-indigo-950/30">
                        <p class="font-semibold text-zinc-950 dark:text-white">Conferir lançamentos</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Marque pagamentos e acompanhe pendências.</p>
                    </a>

                    <button type="button" x-on:click="modal = 'category'" class="group rounded-lg border border-zinc-200 p-4 text-left transition hover:border-indigo-300 hover:bg-indigo-50 dark:border-zinc-800 dark:hover:border-indigo-800 dark:hover:bg-indigo-950/30"><p class="font-semibold text-zinc-950 dark:text-white">Nova categoria</p><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Organize os gráficos sem sair daqui.</p></button>
                </div>
            </section>

            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="inline-flex items-center gap-2 text-base font-semibold text-zinc-950 dark:text-white"><x-ui.icon name="credit-card" class="h-4 w-4 text-indigo-500" />Cartões de crédito</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Uso da fatura no mês selecionado.</p>
                    </div>
                    <a href="{{ route('credit-cards.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Gerenciar</a>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($cards as $card)
                        @php
                            $limit = max((float) $card['limit'], 0);
                            $used = (float) $card['used'];
                            $openUsed = (float) ($card['open_used'] ?? $used);
                            $openAvailable = (float) ($card['open_available'] ?? $card['available']);
                            $percent = $limit > 0 ? min(($openUsed / $limit) * 100, 100) : 0;
                            $barColor = $percent < 55 ? 'bg-emerald-500' : ($percent < 85 ? 'bg-amber-500' : 'bg-rose-500');
                        @endphp

                        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-zinc-950 dark:text-white">{{ $card['card']->name }}</p>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                        Limite usado: R$ {{ number_format($openUsed, 2, ',', '.') }} de R$ {{ number_format($limit, 2, ',', '.') }}
                                    </p>
                                </div>
                                <span class="text-sm font-semibold {{ $percent >= 85 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-600 dark:text-zinc-300' }}">
                                    {{ number_format($percent, 0) }}%
                                </span>
                            </div>

                            <div class="mt-3 h-2 rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div class="h-2 rounded-full {{ $barColor }}" style="width: {{ $percent }}%"></div>
                            </div>

                            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                Fatura {{ $selectedMonthLabel }}: R$ {{ number_format($used, 2, ',', '.') }} - Disponível: R$ {{ number_format($openAvailable, 2, ',', '.') }}
                            </p>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-zinc-300 p-6 text-center dark:border-zinc-700">
                            <p class="font-medium text-zinc-700 dark:text-zinc-200">Nenhum cartão cadastrado</p>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Cadastre cartões para acompanhar faturas aqui.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-1 border-b border-zinc-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800">
                <div>
                    <h2 class="inline-flex items-center gap-2 text-base font-semibold text-zinc-950 dark:text-white"><x-ui.icon name="receipt" class="h-4 w-4 text-indigo-500" />Lançamentos do mês</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Pendências aparecem primeiro para facilitar a conferência.</p>
                </div>
                <a href="{{ route('entries.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Ver todos</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-950/50 dark:text-zinc-400">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Descrição</th>
                            <th class="px-5 py-3 font-semibold">Categoria</th>
                            <th class="px-5 py-3 font-semibold">Data</th>
                            <th class="px-5 py-3 text-right font-semibold">Valor</th>
                            <th class="px-5 py-3 text-right font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($entries as $entry)
                            @php
                                $transaction = $entry->transaction;
                                $type = $transaction?->type;
                                $isIncome = $type === 'income';
                            @endphp

                            <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-950/40">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-zinc-950 dark:text-white">{{ $transaction?->description ?? 'Transação removida' }}</p>
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $entry->account?->name ?? $entry->creditCard?->name ?? 'Sem conta vinculada' }}
                                        @if ($entry->installment_number && $entry->installments_total)
                                            · Parcela {{ $entry->installment_number }}/{{ $entry->installments_total }}
                                        @endif
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                        {{ $transaction?->category?->name ?? 'Sem categoria' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">
                                    <p>{{ ($entry->due_date ?? $entry->reference_date)->format('d/m/Y') }}</p>
                                    @if ($transaction?->start_date && ! $transaction->start_date->isSameMonth($entry->reference_date))
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-500">
                                            Compra: {{ $transaction->start_date->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right font-semibold {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $isIncome ? '+' : '-' }} R$ {{ number_format($entry->value, 2, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button
                                        type="button"
                                        wire:click="toggleEntryStatus({{ $entry->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleEntryStatus({{ $entry->id }})"
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold transition disabled:opacity-60 {{ $entry->status === 'paid' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-950 dark:text-amber-300' }}"
                                    >
                                        {{ $entry->status === 'paid' ? 'Pago' : 'Pendente' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                    Nenhum lançamento encontrado para este mês.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>


    <div
        x-cloak
        x-show="modal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
        x-on:keydown.escape.window="modal = null"
    >
        <div class="absolute inset-0" x-on:click="modal = null"></div>

        <div
            x-show="modal"
            x-transition.scale.origin.center
            class="relative z-10 flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900"
        >
            <div class="flex items-start justify-between gap-4 border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Nova categoria</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Crie uma categoria para organizar seus lançamentos.</p>
                </div>
                <button
                    type="button"
                    x-on:click="modal = null"
                    class="rounded-lg p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white"
                    aria-label="Fechar"
                >
                    <x-ui.icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-5">
                <div x-show="modal === 'category'">
                    <livewire:categories.category-form :modal="true" :key="'dashboard-category-modal'" />
                </div>
            </div>
        </div>
    </div>
</div>


