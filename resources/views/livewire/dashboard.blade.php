<div class="-m-4 min-h-screen bg-[#eef1f7] p-4 text-zinc-950 sm:-m-6 sm:p-6 lg:-m-8 lg:p-8" x-data="{ modal: null }" x-init="$nextTick(() => window.FinanceCharts?.dashboardFromPage())" x-on:close-dashboard-modal.window="modal = null">
    <script type="application/json" data-dashboard-chart-payload>@json(["monthly" => $monthlyChart, "yearly" => $yearlyChart, "categories" => $categoryChart])</script>

    @php
        $income = (float) ($summary['income'] ?? 0);
        $expenses = (float) ($summary['expenses'] ?? 0);
        $balance = (float) ($summary['final_balance'] ?? 0);
        $pendingTotal = (float) ($health['pending_total'] ?? 0);
        $movementTotal = max((float) ($health['movement_total'] ?? 0), 1);
        $incomeShare = min(round(($income / $movementTotal) * 100), 100);
        $expenseShare = min(round(($expenses / $movementTotal) * 100), 100);
        $pendingShare = min(round(($pendingTotal / max($pendingTotal + $income + $expenses, 1)) * 100), 100);
        $primaryCard = $cards[0] ?? null;
    @endphp

    <div class="mx-auto max-w-[1500px] overflow-hidden rounded-[2rem] border border-white/80 bg-[#f8f9fc] p-4 shadow-2xl shadow-slate-400/30 sm:p-5 lg:p-6">
        <div class="flex justify-end">
            <div class="flex flex-wrap items-center justify-end gap-2">
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
                    class="flex items-center rounded-full bg-white p-1 shadow-sm ring-1 ring-zinc-200"
                >
                    <a href="{{ route('dashboard', ['month' => $previousMonth]) }}" class="flex h-10 w-10 items-center justify-center rounded-full text-lg font-semibold text-zinc-600 transition hover:bg-zinc-100" aria-label="Mês anterior">‹</a>
                    <button type="button" x-on:click="openMonthPicker()" class="relative flex h-10 min-w-32 items-center justify-center rounded-full px-4 text-sm font-semibold text-zinc-950 transition hover:bg-zinc-100" aria-label="Escolher mês">
                        {{ $selectedMonthLabel }}
                        <input
                            x-ref="monthPicker"
                            type="month"
                            value="{{ $month }}"
                            onchange="if (this.value) window.location.href = '{{ route('dashboard') }}?month=' + this.value"
                            class="pointer-events-none absolute inset-0 h-full w-full opacity-0"
                            aria-label="Escolher mês"
                        />
                    </button>
                    <a href="{{ route('dashboard', ['month' => $nextMonth]) }}" class="flex h-10 w-10 items-center justify-center rounded-full text-lg font-semibold text-zinc-600 transition hover:bg-zinc-100" aria-label="Próximo mês">›</a>
                </div>

                <button type="button" x-on:click="modal = 'transaction'" class="inline-flex h-11 items-center justify-center gap-2 rounded-full bg-indigo-600 px-5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-500">
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    Nova
                </button>
            </div>
        </div>

        <div
            wire:loading.delay
            wire:target="month,toggleEntryStatus"
            class="mt-5 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm font-medium text-indigo-700"
        >
            Atualizando os dados do dashboard...
        </div>

        <div wire:loading.class="opacity-60 pointer-events-none" wire:target="month,toggleEntryStatus" class="mt-5 grid gap-5 transition-opacity xl:grid-cols-[1fr_360px]">
            <main class="space-y-5">
                <div class="grid gap-4 md:grid-cols-3">
                    <a href="{{ route('reports.income', ['month' => $month]) }}" wire:navigate class="group min-h-40 rounded-3xl bg-indigo-600 p-5 text-white shadow-xl shadow-indigo-200 transition hover:-translate-y-0.5">
                        <div class="flex items-start justify-between">
                            <p class="text-sm font-medium text-indigo-100">Receitas</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-indigo-600"><x-ui.icon name="arrow-up" class="h-5 w-5" /></span>
                        </div>
                        <p class="mt-8 text-4xl font-semibold leading-none tracking-tight">R$ {{ number_format($income, 2, ',', '.') }}</p>
                        <span class="mt-4 inline-flex rounded-full border border-white/40 px-3 py-1 text-xs font-semibold text-white">{{ $incomeShare }}% do movimento</span>
                    </a>

                    <a href="{{ route('reports.expenses', ['month' => $month]) }}" wire:navigate class="group min-h-40 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200/70 transition hover:-translate-y-0.5 hover:shadow-lg">
                        <div class="flex items-start justify-between">
                            <p class="text-sm font-medium text-zinc-500">Despesas</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 text-zinc-950"><x-ui.icon name="arrow-down" class="h-5 w-5" /></span>
                        </div>
                        <p class="mt-8 text-4xl font-semibold leading-none tracking-tight">R$ {{ number_format($expenses, 2, ',', '.') }}</p>
                        <span class="mt-4 inline-flex rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-600">{{ $expenseShare }}% do movimento</span>
                    </a>

                    <div class="min-h-40 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200/70">
                        <div class="flex items-start justify-between">
                            <p class="text-sm font-medium text-zinc-500">Pendências</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 text-zinc-950"><x-ui.icon name="calendar" class="h-5 w-5" /></span>
                        </div>
                        <p class="mt-8 text-4xl font-semibold leading-none tracking-tight">R$ {{ number_format($pendingTotal, 2, ',', '.') }}</p>
                        <div class="mt-4 h-2 rounded-full bg-zinc-100">
                            <div class="h-2 rounded-full bg-amber-400" style="width: {{ $pendingShare }}%"></div>
                        </div>
                    </div>
                </div>

                <section class="overflow-hidden rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200/70 lg:p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500">Saldo total</p>
                            <div class="mt-3 flex flex-wrap items-end gap-3">
                                <h1 class="text-4xl font-semibold leading-none tracking-tight sm:text-5xl">R$ {{ number_format($balance, 2, ',', '.') }}</h1>
                                <span class="mb-1 inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $health['payment_progress'] ?? 0 }}% pago</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="rounded-full border border-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-700">Ano {{ $selectedMonthYear }}</span>
                            <button type="button" x-on:click="modal = 'transaction'" class="flex h-10 w-10 items-center justify-center rounded-full border border-zinc-200 text-zinc-700 transition hover:bg-zinc-100" aria-label="Nova transação">
                                <x-ui.icon name="plus" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <div wire:ignore class="mt-6 h-[310px] overflow-hidden" data-dashboard-chart="yearly"></div>
                </section>

                <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-zinc-200/70">
                    <div class="flex flex-col gap-3 border-b border-zinc-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold">Transações recentes</h2>
                            <p class="mt-1 text-sm text-zinc-500">Pendências aparecem primeiro para facilitar a conferência.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('entries.index') }}" wire:navigate class="rounded-full border border-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100">Ver todos</a>
                            <button type="button" x-on:click="modal = 'transaction'" class="rounded-full bg-zinc-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-800">Adicionar</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-400">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Descrição</th>
                                    <th class="px-5 py-3 font-semibold">Meio</th>
                                    <th class="px-5 py-3 font-semibold">Categoria</th>
                                    <th class="px-5 py-3 text-right font-semibold">Status</th>
                                    <th class="px-5 py-3 text-right font-semibold">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse ($entries as $entry)
                                    @php
                                        $transaction = $entry->transaction;
                                        $type = $transaction?->type;
                                        $isIncome = $type === 'income';
                                    @endphp

                                    <tr class="transition hover:bg-zinc-50">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-10 w-10 items-center justify-center rounded-full {{ $isIncome ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-950 text-white' }}">
                                                    <x-ui.icon :name="$isIncome ? 'arrow-up' : 'receipt'" class="h-4 w-4" />
                                                </span>
                                                <div>
                                                    <p class="font-semibold text-zinc-950">{{ $transaction?->description ?? 'Transação removida' }}</p>
                                                    <p class="mt-1 text-xs text-zinc-500">{{ ($entry->due_date ?? $entry->reference_date)->format('d/m/Y') }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-zinc-600">
                                            {{ $entry->account?->name ?? $entry->creditCard?->name ?? 'Sem vínculo' }}
                                            @if ($entry->installment_number && $entry->installments_total)
                                                <span class="block text-xs text-zinc-400">Parcela {{ $entry->installment_number }}/{{ $entry->installments_total }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex rounded-full border border-zinc-200 bg-white px-3 py-1 text-xs font-medium text-zinc-700">
                                                {{ $transaction?->category?->name ?? 'Sem categoria' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <button
                                                type="button"
                                                wire:click="toggleEntryStatus({{ $entry->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="toggleEntryStatus({{ $entry->id }})"
                                                class="inline-flex rounded-full px-3 py-1 text-xs font-semibold transition disabled:opacity-60 {{ $entry->status === 'paid' ? 'bg-emerald-500 text-white hover:bg-emerald-600' : 'bg-amber-100 text-amber-700 hover:bg-amber-200' }}"
                                            >
                                                {{ $entry->status === 'paid' ? 'Pago' : 'Pendente' }}
                                            </button>
                                        </td>
                                        <td class="px-5 py-4 text-right font-semibold {{ $isIncome ? 'text-emerald-600' : 'text-zinc-950' }}">
                                            {{ $isIncome ? '+' : '-' }} R$ {{ number_format($entry->value, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-zinc-500">Nenhum lançamento encontrado para este mês.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <aside class="space-y-5">
                <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200/70">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold">Cartões</h2>
                            <p class="mt-1 text-sm text-zinc-500">Visão geral do limite comprometido.</p>
                        </div>
                        <a href="{{ route('credit-cards.index') }}" wire:navigate class="shrink-0 rounded-lg border border-zinc-200 px-3 py-2 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50">
                            Gerenciar
                        </a>
                    </div>

                    @if (count($cards))
                        @php
                            $cardsLimit = collect($cards)->sum(fn ($cardData) => (float) $cardData['limit']);
                            $cardsUsed = collect($cards)->sum(fn ($cardData) => (float) ($cardData['open_used'] ?? $cardData['used']));
                            $cardsAvailable = $cardsLimit - $cardsUsed;
                            $cardsPercent = $cardsLimit > 0 ? min(($cardsUsed / $cardsLimit) * 100, 100) : 0;
                        @endphp

                        <div class="mt-5 grid grid-cols-3 divide-x divide-zinc-200 rounded-xl border border-zinc-200 bg-zinc-50 py-3 text-center">
                            <div class="px-2">
                                <p class="text-xs text-zinc-500">Limite total</p>
                                <p class="mt-1 text-sm font-bold text-zinc-950">R$ {{ number_format($cardsLimit, 2, ',', '.') }}</p>
                            </div>
                            <div class="px-2">
                                <p class="text-xs text-zinc-500">Usado</p>
                                <p class="mt-1 text-sm font-bold text-rose-600">R$ {{ number_format($cardsUsed, 2, ',', '.') }}</p>
                            </div>
                            <div class="px-2">
                                <p class="text-xs text-zinc-500">Disponível</p>
                                <p class="mt-1 text-sm font-bold {{ $cardsAvailable < 0 ? 'text-rose-600' : 'text-emerald-600' }}">R$ {{ number_format($cardsAvailable, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-zinc-100">
                            <div class="h-full rounded-full {{ $cardsPercent >= 90 ? 'bg-rose-500' : ($cardsPercent >= 70 ? 'bg-amber-500' : 'bg-indigo-600') }}" style="width: {{ $cardsPercent }}%"></div>
                        </div>
                        <div class="mt-1 flex justify-between text-xs text-zinc-500">
                            <span>{{ number_format($cardsPercent, 0) }}% utilizado</span>
                            <span>{{ count($cards) }} {{ count($cards) === 1 ? 'cartão' : 'cartões' }}</span>
                        </div>

                        <div class="mt-5 divide-y divide-zinc-200 border-y border-zinc-200">
                            @foreach ($cards as $cardData)
                                @php
                                    $limit = max((float) $cardData['limit'], 0);
                                    $openUsed = (float) ($cardData['open_used'] ?? $cardData['used']);
                                    $openAvailable = (float) ($cardData['open_available'] ?? $cardData['available']);
                                    $percent = $limit > 0 ? min(($openUsed / $limit) * 100, 100) : 0;
                                @endphp

                                <div class="py-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-zinc-950">{{ $cardData['card']->name }}</p>
                                            <p class="mt-0.5 text-xs text-zinc-500">Vence dia {{ $cardData['card']->due_day }}</p>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <p class="font-bold text-zinc-950">R$ {{ number_format($openUsed, 2, ',', '.') }}</p>
                                            <p class="mt-0.5 text-xs text-zinc-500">de R$ {{ number_format($limit, 2, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-zinc-100">
                                        <div class="h-full rounded-full {{ $percent >= 90 ? 'bg-rose-500' : ($percent >= 70 ? 'bg-amber-500' : 'bg-indigo-600') }}" style="width: {{ $percent }}%"></div>
                                    </div>

                                    <div class="mt-2 flex justify-between text-xs">
                                        <span class="text-zinc-500">{{ number_format($percent, 0) }}% usado</span>
                                        <span class="font-semibold {{ $openAvailable < 0 ? 'text-rose-600' : 'text-emerald-600' }}">R$ {{ number_format($openAvailable, 2, ',', '.') }} disponível</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-5 rounded-3xl border border-dashed border-zinc-300 p-6 text-center">
                            <p class="font-semibold text-zinc-800">Nenhum cartão cadastrado</p>
                            <p class="mt-1 text-sm text-zinc-500">Cadastre cartões para acompanhar faturas aqui.</p>
                        </div>
                    @endif
                </section>

                <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200/70">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold">Resumo</h2>
                        <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-600">{{ $selectedMonthLabel }}</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200"><x-ui.icon name="arrow-up" class="h-4 w-4" /></span>
                                <span class="text-zinc-600">Receitas no mês</span>
                            </div>
                            <span class="text-right font-semibold">R$ {{ number_format($income, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200"><x-ui.icon name="arrow-down" class="h-4 w-4" /></span>
                                <span class="text-zinc-600">Despesas no mês</span>
                            </div>
                            <span class="text-right font-semibold">R$ {{ number_format($expenses, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200/70">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold">Categorias</h2>
                        <a href="{{ route('categories.index') }}" wire:navigate class="text-sm font-semibold text-indigo-600">Gerenciar</a>
                    </div>
                    <div class="mt-4 grid gap-4">
                        <div wire:ignore class="h-[170px] overflow-hidden" data-dashboard-chart="categories"></div>
                        <div wire:ignore class="h-[150px] overflow-hidden" data-dashboard-chart="monthly"></div>
                    </div>
                </section>

                <section class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200/70">
                    <h2 class="text-xl font-semibold">Atalhos</h2>
                    <div class="mt-4 grid gap-3">
                        <button type="button" x-on:click="modal = 'transaction'" class="flex items-center justify-between rounded-2xl border border-zinc-200 p-4 text-left transition hover:bg-zinc-50">
                            <span>
                                <span class="block font-semibold">Registrar transação</span>
                                <span class="mt-1 block text-sm text-zinc-500">Receita, despesa ou parcelamento.</span>
                            </span>
                            <x-ui.icon name="plus" class="h-5 w-5 text-indigo-600" />
                        </button>
                        <button type="button" x-on:click="modal = 'category'" class="flex items-center justify-between rounded-2xl border border-zinc-200 p-4 text-left transition hover:bg-zinc-50">
                            <span>
                                <span class="block font-semibold">Nova categoria</span>
                                <span class="mt-1 block text-sm text-zinc-500">Organize seus relatórios.</span>
                            </span>
                            <x-ui.icon name="tag" class="h-5 w-5 text-indigo-600" />
                        </button>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <div
        x-cloak
        x-show="modal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/55 p-3 backdrop-blur-md sm:p-6"
        x-on:keydown.escape.window="modal = null"
    >
        <div class="absolute inset-0" x-on:click="modal = null"></div>

        <div
            x-show="modal"
            x-transition.scale.origin.center
            class="relative z-10 flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-[2rem] border border-white/80 bg-[#f8f9fc] shadow-2xl shadow-zinc-950/25"
        >
            <div class="flex shrink-0 items-start justify-between gap-4 border-b border-zinc-200/80 bg-white px-5 py-4 sm:px-6">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                        <x-ui.icon name="plus" class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-950" x-text="modal === 'transaction' ? 'Nova transação' : 'Nova categoria'"></h2>
                        <p class="mt-1 text-sm text-zinc-500" x-text="modal === 'transaction' ? 'Registre receita, despesa, cartão ou recorrência.' : 'Crie uma categoria para organizar seus lançamentos.'"></p>
                    </div>
                </div>
                <button
                    type="button"
                    x-on:click="modal = null"
                    class="rounded-full p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900"
                    aria-label="Fechar"
                >
                    <x-ui.icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-5 py-5 sm:px-6">
                <div x-show="modal === 'transaction'">
                    <livewire:transactions.transaction-form :modal="true" :key="'dashboard-transaction-modal'" />
                </div>

                <div x-show="modal === 'category'">
                    <livewire:categories.category-form :modal="true" :key="'dashboard-category-modal'" />
                </div>
            </div>
        </div>
    </div>
</div>
