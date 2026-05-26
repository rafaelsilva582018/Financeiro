<div class="space-y-6" x-data="{ modal: null }" x-on:close-resource-modal.window="modal = null">
    @php
        $incomeTotal = $transactions->where('type', 'income')->sum('total_value');
        $expenseTotal = $transactions->where('type', 'expense')->sum('total_value');
        $fixedCount = $transactions->where('is_fixed', true)->count();
    @endphp

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="flex items-start gap-3">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 shadow-sm dark:bg-indigo-950 dark:text-indigo-300">
                <x-ui.icon name="receipt" class="h-6 w-6" />
            </span>
            <div>
                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Operações</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">Transações</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Cadastre receitas, despesas, parcelas e recorrências em um só lugar.</p>
            </div>
        </div>

        <button type="button" x-on:click="modal = 'transaction'" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
            <x-ui.icon name="plus" class="h-4 w-4" />
            Nova transação
        </button>
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Transações</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"><x-ui.icon name="receipt" class="h-4 w-4" /></span></div>
            <p class="mt-4 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $transactions->count() }}</p>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $fixedCount }} fixas ou recorrentes</p>
        </section>
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Receitas</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300"><x-ui.icon name="arrow-up" class="h-4 w-4" /></span></div>
            <p class="mt-4 text-2xl font-semibold text-emerald-600 dark:text-emerald-400">R$ {{ number_format($incomeTotal, 2, ',', '.') }}</p>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Entradas cadastradas</p>
        </section>
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Despesas</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-300"><x-ui.icon name="arrow-down" class="h-4 w-4" /></span></div>
            <p class="mt-4 text-2xl font-semibold text-rose-600 dark:text-rose-400">R$ {{ number_format($expenseTotal, 2, ',', '.') }}</p>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Saídas cadastradas</p>
        </section>
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between gap-3"><p class="text-sm text-zinc-500 dark:text-zinc-400">Saldo previsto</p><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><x-ui.icon name="chart" class="h-4 w-4" /></span></div>
            <p class="mt-4 text-2xl font-semibold {{ ($incomeTotal - $expenseTotal) >= 0 ? 'text-zinc-950 dark:text-white' : 'text-rose-600 dark:text-rose-400' }}">R$ {{ number_format($incomeTotal - $expenseTotal, 2, ',', '.') }}</p>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Receitas menos despesas</p>
        </section>
    </div>

    <section class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-1 border-b border-zinc-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800">
            <div>
                <h2 class="inline-flex items-center gap-2 text-base font-semibold text-zinc-950 dark:text-white"><x-ui.icon name="receipt" class="h-4 w-4 text-indigo-500" />Histórico de transações</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Veja categoria, forma de pagamento e geração dos lançamentos.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead class="bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:bg-zinc-950/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Transação</th>
                        <th class="px-5 py-3 font-semibold">Categoria</th>
                        <th class="px-5 py-3 font-semibold">Pagamento</th>
                        <th class="px-5 py-3 text-center font-semibold">Data</th>
                        <th class="px-5 py-3 text-right font-semibold">Valor</th>
                        <th class="px-5 py-3 text-right font-semibold">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($transactions as $transaction)
                        @php
                            $isIncome = $transaction->type === 'income';
                            $badge = $transaction->is_fixed ? 'Fixa' : ($transaction->installments ? $transaction->installments . ' parcelas' : 'Única');
                            $payment = $transaction->creditCard?->name ?? $transaction->account?->name ?? 'Sem vínculo';
                        @endphp
                        <tr class="transition hover:bg-zinc-50 dark:hover:bg-zinc-950/40">
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $isIncome ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-300' }}"><x-ui.icon :name="$isIncome ? 'arrow-up' : 'arrow-down'" class="h-4 w-4" /></span>
                                    <div>
                                        <p class="font-semibold text-zinc-950 dark:text-white">{{ $transaction->description }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $isIncome ? 'Receita' : 'Despesa' }} · {{ $badge }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4"><span class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $transaction->category?->name ?? 'Sem categoria' }}</span></td>
                            <td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">{{ $payment }}</td>
                            <td class="px-5 py-4 text-center text-zinc-600 dark:text-zinc-300">{{ $transaction->start_date->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-right text-base font-semibold {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">{{ $isIncome ? '+' : '-' }} R$ {{ number_format($transaction->total_value, 2, ',', '.') }}</td>
                            <td class="px-5 py-4 text-right"><div class="inline-flex gap-2"><a href="{{ route('transactions.edit', $transaction->id) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-100 px-3 py-1.5 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700"><x-ui.icon name="edit" class="h-3.5 w-3.5" />Editar</a><button type="button" wire:click="delete({{ $transaction->id }})" wire:confirm="Tem certeza que deseja excluir?" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-rose-500"><x-ui.icon name="trash" class="h-3.5 w-3.5" />Excluir</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center"><div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300"><x-ui.icon name="receipt" class="h-6 w-6" /></div><p class="mt-3 font-semibold text-zinc-900 dark:text-white">Nenhuma transação cadastrada</p><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Comece registrando sua primeira receita ou despesa.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div x-cloak x-show="modal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/65 p-3 backdrop-blur-sm sm:p-6" x-on:keydown.escape.window="modal = null">
        <div class="absolute inset-0" x-on:click="modal = null"></div>
        <div x-show="modal" x-transition.scale.origin.center class="relative z-10 flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex shrink-0 items-start justify-between gap-4 border-b border-zinc-200 px-5 py-4 dark:border-zinc-800 sm:px-6">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300"><x-ui.icon name="receipt" class="h-5 w-5" /></span>
                    <div><h2 class="text-lg font-semibold text-zinc-950 dark:text-white">Nova transação</h2><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Preencha os dados e salve sem sair desta página.</p></div>
                </div>
                <button type="button" x-on:click="modal = null" class="rounded-lg p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="Fechar"><x-ui.icon name="x" class="h-5 w-5" /></button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-5 sm:px-6">
                <livewire:transactions.transaction-form :modal="true" :key="'transactions-create-modal'" />
            </div>
        </div>
    </div>
</div>