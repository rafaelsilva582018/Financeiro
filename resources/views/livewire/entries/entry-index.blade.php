<div class="space-y-6">
    @php
        $paidEntries = $entries->where('status', 'paid');
        $pendingEntries = $entries->where('status', 'pending');
        $paidTotal = $paidEntries->sum('value');
        $pendingTotal = $pendingEntries->sum('value');
        $progress = $entries->count() ? round(($paidEntries->count() / $entries->count()) * 100) : 0;
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="flex items-start gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 shadow-sm dark:bg-indigo-950 dark:text-indigo-300"><x-ui.icon name="calendar" class="h-6 w-6" /></span>
            <div>
                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Operações</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">Lançamentos</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Controle vencimentos, pagamentos e pendências do mês.</p>
            </div>
        </div>

        <label class="block w-full text-sm font-medium text-zinc-600 sm:w-80 dark:text-zinc-300">
            Mês de referência
            <input type="month" wire:model.live="month" class="mt-1 h-11 w-full rounded-lg border border-zinc-200 bg-white px-3 text-sm font-medium text-zinc-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white">
        </label>
    </div>

    <div wire:loading.delay wire:target="month" class="flex items-center gap-2 rounded-lg border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-700 dark:border-indigo-900/50 dark:bg-indigo-950/40 dark:text-indigo-300">
        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
        Atualizando lançamentos...
    </div>

    <div wire:loading.class="opacity-60 pointer-events-none" wire:target="month" class="space-y-6 transition-opacity">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"><div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Lançamentos</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><x-ui.icon name="receipt" class="h-4 w-4" /></span></div><p class="mt-4 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $entries->count() }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $progress }}% conferidos</p></section>
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"><div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Pagos</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><x-ui.icon name="check" class="h-4 w-4" /></span></div><p class="mt-4 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">R$ {{ number_format($paidTotal, 2, ',', '.') }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $paidEntries->count() }} pagos</p></section>
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"><div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Pendentes</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-300"><x-ui.icon name="calendar" class="h-4 w-4" /></span></div><p class="mt-4 text-2xl font-semibold text-amber-600 dark:text-amber-400">R$ {{ number_format($pendingTotal, 2, ',', '.') }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $pendingEntries->count() }} em aberto</p></section>
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900"><div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Progresso</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><x-ui.icon name="chart" class="h-4 w-4" /></span></div><div class="mt-5 h-2 rounded-full bg-zinc-100 dark:bg-zinc-800"><div class="h-2 rounded-full bg-indigo-600" style="width: {{ $progress }}%"></div></div><p class="mt-3 text-sm font-semibold text-zinc-950 dark:text-white">{{ $progress }}%</p></section>
        </div>

        <section class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-1 border-b border-zinc-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800">
                <div><h2 class="inline-flex items-center gap-2 text-base font-semibold text-zinc-950 dark:text-white"><x-ui.icon name="calendar" class="h-4 w-4 text-indigo-500" />Agenda do mês</h2><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Pendências e pagamentos com origem, categoria e vencimento.</p></div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1020px] text-left text-sm">
                    <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-950/50 dark:text-zinc-400"><tr><th class="px-5 py-3 font-semibold">Lançamento</th><th class="px-5 py-3 font-semibold">Categoria</th><th class="px-5 py-3 font-semibold">Origem</th><th class="px-5 py-3 text-center font-semibold">Parcela</th><th class="px-5 py-3 text-center font-semibold">Vencimento</th><th class="px-5 py-3 text-right font-semibold">Valor</th><th class="px-5 py-3 text-right font-semibold">Status</th></tr></thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($entries as $entry)
                            @php
                                $transaction = $entry->transaction;
                                $type = $transaction?->type;
                                $isIncome = $type === 'income';
                                $origin = $entry->account?->name ?? $entry->creditCard?->name ?? 'Sem conta vinculada';
                            @endphp
                            <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-950/40">
                                <td class="px-5 py-4"><div class="flex items-start gap-3"><span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $isIncome ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-300' }}"><x-ui.icon :name="$isIncome ? 'arrow-up' : 'arrow-down'" class="h-4 w-4" /></span><div><p class="font-semibold text-zinc-950 dark:text-white">{{ $transaction?->description ?? 'Transação removida' }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $isIncome ? 'Receita' : 'Despesa' }}@if ($entry->installment_number && $entry->installments_total) · Parcela {{ $entry->installment_number }}/{{ $entry->installments_total }} @endif</p></div></div></td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $transaction?->category?->name ?? 'Sem categoria' }}</span></td>
                                <td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">{{ $origin }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if ($entry->installment_number && $entry->installments_total)
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">{{ $entry->installment_number }}/{{ $entry->installments_total }}</span>
                                    @else
                                        <span class="text-xs font-medium text-zinc-400">Única</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center text-zinc-600 dark:text-zinc-300">
                                    <p>{{ ($entry->due_date ?? $entry->reference_date)->format('d/m/Y') }}</p>
                                    @if ($transaction?->start_date && ! $transaction->start_date->isSameMonth($entry->reference_date))
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-500">
                                            Compra: {{ $transaction->start_date->format('d/m/Y') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right text-base font-semibold {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">{{ $isIncome ? '+' : '-' }} R$ {{ number_format($entry->value, 2, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right"><button wire:click="toggleStatus({{ $entry->id }})" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold transition disabled:opacity-60 {{ $entry->status === 'paid' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-950 dark:text-amber-300' }}"><x-ui.icon :name="$entry->status === 'paid' ? 'check' : 'calendar'" class="h-3.5 w-3.5" />{{ $entry->status === 'paid' ? 'Pago' : 'Marcar pago' }}</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-12 text-center"><div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300"><x-ui.icon name="calendar" class="h-6 w-6" /></div><p class="mt-3 font-semibold text-zinc-900 dark:text-white">Nenhum lançamento neste mês</p><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Quando criar transações, os lançamentos aparecem aqui.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
