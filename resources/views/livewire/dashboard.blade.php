<div class="p-6 space-y-6">
    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Dashboard</h1>

        <input type="month" wire:model.live="month" class="border rounded p-2" />
    </div>

    {{-- 🔄 FEEDBACK DE CARREGAMENTO (NOVO) --}}
    <div
        wire:loading.delay
        wire:target="month"
        class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400"
    >
        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        Atualizando dashboard…
    </div>

    {{-- 🔒 WRAPPER DE CONTEÚDO (NOVO) --}}
    <div
        wire:loading.class="opacity-50 pointer-events-none"
        wire:target="month"
        class="space-y-6 transition-opacity"
    >

    {{-- Cards principais --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Saldo --}}
        <div
            class="relative overflow-hidden rounded-xl border bg-white dark:bg-zinc-900 p-5 shadow-sm hover:shadow-md transition"
        >
            <div class="absolute inset-x-0 top-0 h-1 bg-indigo-500"></div>

            <div class="flex items-center justify-between">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo do mês</h2>

                <span class="text-indigo-500 text-xl">💰</span>
            </div>

            <p class="mt-3 text-3xl font-bold tracking-tight">
                R$ {{ number_format($this->summary['final_balance'] ?? 0, 2, ',', '.') }}
            </p>
        </div>

        {{-- Receitas --}}
        <div
            class="relative overflow-hidden rounded-xl border bg-white dark:bg-zinc-900 p-5 shadow-sm hover:shadow-md transition"
        >
            <div class="absolute inset-x-0 top-0 h-1 bg-emerald-500"></div>

            <div class="flex items-center justify-between">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Receitas</h2>

                <span class="text-emerald-500 text-xl">⬆️</span>
            </div>

            <p class="mt-3 text-3xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">
                R$ {{ number_format($this->summary['income'] ?? 0, 2, ',', '.') }}
            </p>
        </div>

        {{-- Despesas --}}
        <div
            class="relative overflow-hidden rounded-xl border bg-white dark:bg-zinc-900 p-5 shadow-sm hover:shadow-md transition"
        >
            <div class="absolute inset-x-0 top-0 h-1 bg-rose-500"></div>

            <div class="flex items-center justify-between">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Despesas</h2>

                <span class="text-rose-500 text-xl">⬇️</span>
            </div>

            <p class="mt-3 text-3xl font-bold tracking-tight text-rose-600 dark:text-rose-400">
                R$ {{ number_format($this->summary['expenses'] ?? 0, 2, ',', '.') }}
            </p>
        </div>
    </div>
    {{-- Acesso rápido --}}
    <div class="rounded-xl border bg-white dark:bg-zinc-900 p-5 shadow-sm">
        <h2 class="text-lg font-semibold mb-5">Acesso rápido</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Nova transação --}}
            <a
                href="{{ route('transactions.create') }}"
                class="group flex items-center justify-between rounded-lg border bg-indigo-600 text-white px-5 py-4 shadow-sm hover:bg-indigo-500 transition"
            >
                <div>
                    <p class="font-medium">Nova transação</p>
                    <p class="text-xs text-indigo-100">Registrar receita ou despesa</p>
                </div>

                <span class="text-xl transition group-hover:translate-x-1"> ➕ </span>
            </a>

            {{-- Ver lançamentos --}}
            <a
                href="{{ route('entries.index') }}"
                class="group flex items-center justify-between rounded-lg border bg-gray-50 dark:bg-zinc-800 px-5 py-4 hover:bg-gray-100 dark:hover:bg-zinc-700 transition"
            >
                <div>
                    <p class="font-medium">Ver lançamentos</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Histórico completo</p>
                </div>

                <span class="text-xl transition group-hover:translate-x-1"> 📄 </span>
            </a>
        </div>
    </div>

    {{-- Gráfico anual --}}
    <div class="rounded border p-4 bg-white dark:bg-zinc-900">
        <h2 class="text-lg font-semibold mb-4">Receitas x Despesas — Ano</h2>

        <div id="chart-yearly" wire:ignore></div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Receita x Despesa --}}
        <div class="rounded border p-4 bg-white dark:bg-zinc-900">
            <h2 class="text-lg font-semibold mb-4">Receitas x Despesas</h2>

            <div id="chart-balance" wire:ignore></div>
        </div>

        {{-- Despesas por categoria --}}
        <div class="rounded border p-4 bg-white dark:bg-zinc-900">
            <h2 class="text-lg font-semibold mb-4">Despesas por categoria</h2>

            <div id="chart-categories" wire:ignore></div>
        </div>
    </div>

    {{-- Scripts dos gráficos --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const isDark = () => document.documentElement.classList.contains("dark");

            const themeColors = () => ({
                text: isDark() ? "#e5e7eb" : "#374151", // texto
                grid: isDark() ? "#3f3f46" : "#e5e7eb", // linhas
                tooltipBg: isDark() ? "#18181b" : "#ffffff",
            });

            /* =====================================================
             * 📊 GRÁFICO ANUAL — Receitas x Despesas
             * ===================================================== */
            const yearlyChart = new ApexCharts(document.querySelector("#chart-yearly"), {
                chart: {
                    type: "bar",
                    height: 350,
                    toolbar: { show: false },
                    foreColor: themeColors().text,
                },
                series: [],
                colors: ["#16a34a", "#dc2626"],
                plotOptions: {
                    bar: {
                        columnWidth: "45%",
                    },
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                    labels: {
                        style: {
                            colors: themeColors().text,
                        },
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: themeColors().text,
                        },
                    },
                },
                grid: {
                    borderColor: themeColors().grid,
                },
                tooltip: {
                    theme: isDark() ? "dark" : "light",
                },
                legend: {
                    position: "top",
                    labels: {
                        colors: themeColors().text,
                    },
                },
            });

            yearlyChart.render();

            /* =====================================================
             * 📊 GRÁFICO MENSAL — Receitas x Despesas
             * ===================================================== */
            const balanceChart = new ApexCharts(document.querySelector("#chart-balance"), {
                chart: {
                    type: "bar",
                    height: 300,
                    foreColor: themeColors().text,
                },
                series: [],
                colors: ["#16a34a", "#dc2626"],
                xaxis: {
                    categories: ["Resumo"],
                    labels: {
                        style: {
                            colors: themeColors().text,
                        },
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: themeColors().text,
                        },
                    },
                },
                grid: {
                    borderColor: themeColors().grid,
                },
                tooltip: {
                    theme: isDark() ? "dark" : "light",
                },
                dataLabels: { enabled: false },
            });

            balanceChart.render();

            /* =====================================================
             * 🥧 GRÁFICO — Despesas por categoria
             * ===================================================== */
            const categoryChart = new ApexCharts(document.querySelector("#chart-categories"), {
                chart: {
                    type: "donut",
                    height: 300,
                    foreColor: themeColors().text,
                },
                series: [],
                labels: [],
                legend: {
                    position: "bottom",
                    labels: {
                        colors: themeColors().text,
                    },
                },
                tooltip: {
                    theme: isDark() ? "dark" : "light",
                },
            });

            categoryChart.render();

            /* =====================================================
             * 🔄 EVENTOS LIVEWIRE
             * ===================================================== */
            window.addEventListener("charts:update", (event) => {
                const data = event.detail[0];

                balanceChart.updateSeries([
                    { name: "Receitas", data: [data.income] },
                    { name: "Despesas", data: [data.expenses] },
                ]);

                categoryChart.updateOptions({
                    labels: data.categories.labels,
                });

                categoryChart.updateSeries(data.categories.values);
            });

            window.addEventListener("charts:yearly", (event) => {
                const data = event.detail[0];

                yearlyChart.updateSeries([
                    { name: "Receitas", data: data.income },
                    { name: "Despesas", data: data.expenses },
                ]);
            });

            /* =====================================================
             * 🌗 ATUALIZAR GRÁFICOS AO TROCAR TEMA
             * ===================================================== */
            const observer = new MutationObserver(() => {
                yearlyChart.updateOptions({ chart: { foreColor: themeColors().text } });
                balanceChart.updateOptions({ chart: { foreColor: themeColors().text } });
                categoryChart.updateOptions({ chart: { foreColor: themeColors().text } });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ["class"],
            });
        });
    </script>

    {{-- Cartões de crédito --}} @if (count($cards))
    <div class="rounded-xl border bg-white dark:bg-zinc-900 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold">Cartões de crédito</h2>

            <span class="text-sm text-gray-400"> {{ count($cards) }} cartão{{ count($cards) > 1 ? 's' : '' }} </span>
        </div>

        <div class="space-y-5">
            @foreach ($this->cards as $card) @php $percent = $card['limit'] > 0 ? ($card['used'] / $card['limit']) * 100
            : 0; @endphp

            <div class="rounded-lg border p-4 bg-gray-50 dark:bg-zinc-800">
                {{-- Cabeçalho --}}
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">💳</span>

                        <span class="font-medium"> {{ $card['card']->name }} </span>
                    </div>

                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        R$ {{ number_format($card['used'], 2, ',', '.') }} / R$ {{ number_format($card['limit'], 2, ',',
                        '.') }}
                    </span>
                </div>

                {{-- Barra de progresso --}}
                <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2.5">
                    <div
                        class="h-2.5 rounded-full transition-all
                                {{ $percent < 50
                                    ? 'bg-emerald-500'
                                    : ($percent < 80
                                        ? 'bg-yellow-500'
                                        : 'bg-rose-500') }}"
                        style="width: {{ min($percent, 100) }}%"
                    ></div>
                </div>

                {{-- Rodapé --}}
                <div class="flex justify-between items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                    <span>
                        Disponível:
                        <strong class="text-gray-700 dark:text-gray-200">
                            R$ {{ number_format($card['available'], 2, ',', '.') }}
                        </strong>
                    </span>

                    <span> {{ number_format($percent, 0) }}% usado </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif 
    {{-- FAB + Modais --}}
    <div
        x-data="{
            open: false,
            modal: null
        }"
        @transaction-created.window="modal = null"
    >
        {{-- FAB --}}
        <div class="fixed bottom-6 right-6 z-50">
            <button
                @click="open = !open"
                class="group relative h-14 w-14 rounded-full bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 flex items-center justify-center transition-all duration-200 hover:bg-indigo-500 focus:outline-none"
                aria-label="Ações rápidas"
            >
                <span
                    class="text-3xl leading-none transition-transform duration-200 group-hover:rotate-90"
                    :class="{ 'rotate-45': open }"
                >
                    +
                </span>
            </button>
        </div>

        {{-- Menu flutuante --}}
        <div
            x-show="open"
            x-transition.origin.bottom.right
            @click.outside="open = false"
            class="fixed bottom-24 right-6 z-50 w-56 rounded-xl border bg-white dark:bg-zinc-900 shadow-xl p-2 space-y-1"
        >
            {{-- Nova despesa --}}
            <button
                @click="modal = 'expense'; open = false"
                class="group flex items-center gap-3 w-full rounded-lg px-4 py-2 text-left hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
            >
                <span class="text-lg">➖</span>
                <div>
                    <p class="font-medium">Nova despesa</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Registrar gasto</p>
                </div>
            </button>

            {{-- Nova receita --}}
            <button
                @click="modal = 'income'; open = false"
                class="group flex items-center gap-3 w-full rounded-lg px-4 py-2 text-left hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
            >
                <span class="text-lg">➕</span>
                <div>
                    <p class="font-medium">Nova receita</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Registrar entrada</p>
                </div>
            </button>

            {{-- Nova categoria --}}
            <button
                @click="modal = 'category'; open = false"
                class="group flex items-center gap-3 w-full rounded-lg px-4 py-2 text-left hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
            >
                <span class="text-lg">🏷️</span>
                <div>
                    <p class="font-medium">Nova categoria</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Organizar lançamentos</p>
                </div>
            </button>
        </div>

        {{-- MODAL --}}
<div
    x-show="modal"
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        @click="modal = null"
    ></div>

    {{-- Container --}}
    <div
        x-transition.scale
        @click.outside="modal = null"
        class="relative z-10
               w-full max-w-2xl
               mx-4
               rounded-2xl
               bg-white dark:bg-zinc-900
               shadow-2xl
               border
               max-h-[90vh]
               flex flex-col"
    >

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-zinc-800">
            <h3 class="text-lg font-semibold">
                <span x-show="modal === 'expense'">Nova despesa</span>
                <span x-show="modal === 'income'">Nova receita</span>
                <span x-show="modal === 'category'">Nova categoria</span>
            </h3>

            <button
                @click="modal = null"
                class="rounded-lg p-2
                       text-gray-500
                       hover:bg-zinc-100 dark:hover:bg-zinc-800
                       transition"
                aria-label="Fechar"
            >
                ✕
            </button>
        </div>

        {{-- Body (scroll aqui) --}}
        <div class="flex-1 overflow-y-auto px-6 py-5">

            {{-- TRANSAÇÃO --}}
            <template x-if="modal === 'expense'">
                <livewire:transactions.transaction-form
                    :type="'expense'"
                />
            </template>

            <template x-if="modal === 'income'">
                <livewire:transactions.transaction-form
                    :type="'income'"
                />
            </template>

            {{-- CATEGORIA --}}
            <template x-if="modal === 'category'">
                <livewire:categories.category-form />
            </template>

        </div>
    </div>
</div>

        {{-- Últimos lançamentos --}}
        <div class="rounded-xl border bg-white dark:bg-zinc-900 p-5 shadow-sm">
            {{-- Cabeçalho --}}
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Últimos lançamentos</h2>

                <a href="{{ route('entries.index') }}" class="text-sm font-medium text-indigo-600 hover:underline">
                    Ver todos
                </a>
            </div>

            {{-- Tabela --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500 dark:text-gray-400">
                            <th class="pb-2 font-medium">Descrição</th>
                            <th class="pb-2 font-medium">Tipo</th>
                            <th class="pb-2 font-medium">Mês</th>
                            <th class="pb-2 font-medium text-right">Valor</th>
                            <th class="pb-2 font-medium text-right">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y dark:divide-zinc-800">
                        @forelse ($entries as $entry)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                            {{-- Descrição --}}
                            <td class="py-3">
                                <p class="font-medium">{{ $entry->transaction->description }}</p>
                            </td>

                            {{-- Tipo --}}
                            <td class="py-3">
                                <span
                                    class="inline-flex items-center gap-1
                                       rounded-full px-2 py-0.5 text-xs font-medium
                                       {{ $entry->transaction->type === 'income'
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
                                            : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}"
                                >
                                    {{ $entry->transaction->type === 'income' ? 'Receita' : 'Despesa' }}
                                </span>
                            </td>

                            {{-- Mês --}}
                            <td class="py-3 text-gray-500 dark:text-gray-400">
                                {{ $entry->reference_date->format('m/Y') }}
                            </td>

                            {{-- Valor --}}
                            <td
                                class="py-3 text-right font-semibold
                                   {{ $entry->transaction->type === 'income'
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : 'text-rose-600 dark:text-rose-400' }}"
                            >
                                R$ {{ number_format($entry->value, 2, ',', '.') }}
                            </td>

                            {{-- Status --}}
                            <td class="py-3 text-right">
                                <button
                                    wire:click="toggleEntryStatus({{ $entry->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleEntryStatus"
                                    class="text-xs font-medium
                                        disabled:opacity-50
                                        disabled:cursor-not-allowed
                                        {{ $entry->status === 'paid'
                                                ? 'text-emerald-600 hover:underline'
                                                : 'text-yellow-600 hover:underline' }}"
                                >
                                    {{ $entry->status === 'paid' ? 'Pago' : 'Pendente' }}
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">Nenhum lançamento neste mês</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>
