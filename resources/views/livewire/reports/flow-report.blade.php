<div class="space-y-6" x-data x-init="$nextTick(() => window.FinanceCharts?.flowFromPage())">
    <script type="application/json" data-flow-chart-payload>@json(["categories" => $categoryChart, "monthly" => $monthlyChart, "type" => $type])</script>
    @php
        $isIncome = $type === 'income';
        $accent = $isIncome ? 'emerald' : 'rose';
        $routeType = $isIncome ? 'receitas' : 'despesas';
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="flex items-start gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl {{ $isIncome ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-300' }} shadow-sm">
                <x-ui.icon :name="$isIncome ? 'arrow-up' : 'arrow-down'" class="h-6 w-6" />
            </span>
            <div>
                <p class="text-sm font-medium {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">Relatórios</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $title }}</h1>
                <p class="mt-1 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</p>
            </div>
        </div>

        <label class="block w-full text-sm font-medium text-zinc-600 sm:w-80 dark:text-zinc-300">
            Mês de referência
            <input type="month" wire:model.live="month" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
        </label>
    </div>

    <div wire:loading.delay wire:target="month" class="rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-700 dark:border-indigo-900/50 dark:bg-indigo-950/40 dark:text-indigo-300">
        Atualizando relatório...
    </div>

    <div wire:loading.class="opacity-60 pointer-events-none" wire:target="month" class="space-y-6 transition-opacity">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Total do mês</p><span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $isIncome ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-300' }}"><x-ui.icon :name="$isIncome ? 'arrow-up' : 'arrow-down'" class="h-4 w-4" /></span></div>
                <p class="mt-4 text-2xl font-semibold {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">R$ {{ number_format($summary['total'] ?? 0, 2, ',', '.') }}</p>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $selectedMonthLabel }}</p>
            </section>
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"><div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Confirmado</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><x-ui.icon name="check" class="h-4 w-4" /></span></div><p class="mt-4 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">R$ {{ number_format($summary['paid'] ?? 0, 2, ',', '.') }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Lançamentos pagos</p></section>
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"><div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Em aberto</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-300"><x-ui.icon name="calendar" class="h-4 w-4" /></span></div><p class="mt-4 text-2xl font-semibold text-amber-600 dark:text-amber-400">R$ {{ number_format($summary['pending'] ?? 0, 2, ',', '.') }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Ainda pendente</p></section>
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"><div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Média</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><x-ui.icon name="chart" class="h-4 w-4" /></span></div><p class="mt-4 text-2xl font-semibold text-zinc-950 dark:text-white">R$ {{ number_format($summary['average'] ?? 0, 2, ',', '.') }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Por lançamento</p></section>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section class="h-[380px] overflow-hidden rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-3">
                    <div><h2 class="inline-flex items-center gap-2 text-base font-semibold text-zinc-950 dark:text-white"><x-ui.icon name="chart" class="h-4 w-4 text-indigo-500" />Últimos 6 meses</h2><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Comparação mensal para encontrar tendência.</p></div>
                    @if (! is_null($comparison['variation'] ?? null))
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ ($comparison['variation'] ?? 0) >= 0 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">{{ ($comparison['variation'] ?? 0) >= 0 ? '+' : '' }}{{ $comparison['variation'] }}%</span>
                    @endif
                </div>
                <div wire:ignore class="mt-4 h-[285px] overflow-hidden" data-flow-chart="monthly"></div>
            </section>

            <section class="h-[380px] overflow-hidden rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div><h2 class="inline-flex items-center gap-2 text-base font-semibold text-zinc-950 dark:text-white"><x-ui.icon name="tags" class="h-4 w-4 text-indigo-500" />Por categoria</h2><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Principais categorias no mês.</p></div>
                <div wire:ignore class="mt-4 h-[285px] overflow-hidden" data-flow-chart="categories"></div>
            </section>
        </div>

        <section class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-1 border-b border-zinc-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800">
                <div><h2 class="inline-flex items-center gap-2 text-base font-semibold text-zinc-950 dark:text-white"><x-ui.icon name="receipt" class="h-4 w-4 text-indigo-500" />Detalhamento</h2><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Lançamentos que compõem este relatório.</p></div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-950/50 dark:text-zinc-400"><tr><th class="px-5 py-3 font-semibold">Descrição</th><th class="px-5 py-3 font-semibold">Categoria</th><th class="px-5 py-3 font-semibold">Origem</th><th class="px-5 py-3 text-center font-semibold">Parcela</th><th class="px-5 py-3 text-center font-semibold">Data</th><th class="px-5 py-3 text-right font-semibold">Valor</th><th class="px-5 py-3 text-right font-semibold">Status</th></tr></thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($entries as $entry)
                            @php $origin = $entry->account?->name ?? $entry->creditCard?->name ?? 'Sem conta vinculada'; @endphp
                            <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-950/40"><td class="px-5 py-4 font-semibold text-zinc-950 dark:text-white">{{ $entry->transaction?->description ?? 'Transação removida' }}</td><td class="px-5 py-4"><span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $entry->transaction?->category?->name ?? 'Sem categoria' }}</span></td><td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">{{ $origin }}</td><td class="px-5 py-4 text-center">@if ($entry->installment_number && $entry->installments_total)<span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ $entry->installment_number }}/{{ $entry->installments_total }}</span>@else<span class="text-xs font-medium text-zinc-400">Única</span>@endif</td><td class="px-5 py-4 text-center text-zinc-600 dark:text-zinc-300">{{ ($entry->due_date ?? $entry->reference_date)->format('d/m/Y') }}</td><td class="px-5 py-4 text-right text-base font-semibold {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">{{ $isIncome ? '+' : '-' }} R$ {{ number_format($entry->value, 2, ',', '.') }}</td><td class="px-5 py-4 text-right"><span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $entry->status === 'paid' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}"><x-ui.icon :name="$entry->status === 'paid' ? 'check' : 'calendar'" class="h-3.5 w-3.5" />{{ $entry->status === 'paid' ? 'Pago' : 'Pendente' }}</span></td></tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-12 text-center text-zinc-500 dark:text-zinc-400">Nenhum lançamento encontrado para este mês.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
