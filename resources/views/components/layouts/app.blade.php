<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-[#eef1f7] text-zinc-950">
        @php
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
                ['label' => 'Transações', 'route' => 'transactions.index', 'active' => 'transactions.*'],
                ['label' => 'Cartões', 'route' => 'credit-cards.index', 'active' => 'credit-cards.*'],
                ['label' => 'Lançamentos', 'route' => 'entries.index', 'active' => 'entries.*'],
                ['label' => 'Contas', 'route' => 'accounts.index', 'active' => 'accounts.*'],
                ['label' => 'Categorias', 'route' => 'categories.index', 'active' => 'categories.*'],
                ['label' => 'Banco', 'route' => 'bank-imports.index', 'active' => 'bank-imports.*'],
                ['label' => 'Receitas', 'route' => 'reports.income', 'active' => 'reports.income'],
                ['label' => 'Despesas', 'route' => 'reports.expenses', 'active' => 'reports.expenses'],
            ];
        @endphp

        <header class="sticky top-0 z-40 border-b border-white/80 bg-[#eef1f7]/90 px-4 py-3 backdrop-blur-xl sm:px-6 lg:px-8">
            <div class="mx-auto flex max-w-[1500px] flex-col gap-3 rounded-[1.5rem] bg-white/80 p-2 shadow-sm ring-1 ring-zinc-200/70 lg:flex-row lg:items-center lg:justify-between">
                <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-3 px-2">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-bold text-white shadow-lg shadow-indigo-200">F</div>
                    <div class="leading-tight">
                        <p class="text-lg font-semibold">Finance Online</p>
                        <p class="text-xs font-medium text-zinc-500">Painel financeiro</p>
                    </div>
                </a>

                <nav class="no-scrollbar flex min-w-0 flex-1 items-center gap-1 overflow-x-auto rounded-full bg-white p-1 shadow-inner ring-1 ring-zinc-100 lg:max-w-4xl lg:justify-center">
                    @foreach ($navItems as $item)
                        @php($isActive = request()->routeIs($item['active']))
                        <a
                            href="{{ route($item['route']) }}"
                            class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ $isActive ? 'bg-zinc-950 text-white shadow-sm' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex shrink-0 items-center justify-between gap-2 px-1 lg:justify-end">
                    <flux:dropdown position="bottom" align="end">
                        <button type="button" class="flex h-11 items-center gap-2 rounded-full border border-zinc-200 bg-white px-2.5 pr-4 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 text-xs text-zinc-700">
                                {{ auth()->user()->initials() }}
                            </span>
                            <span class="hidden max-w-28 truncate sm:block">{{ auth()->user()->name }}</span>
                        </button>

                        <flux:menu class="w-[240px]">
                            <div class="px-2 py-1.5 text-sm">
                                <p class="truncate font-semibold text-zinc-950 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</p>
                            </div>

                            <flux:menu.separator />

                            <flux:menu.item :href="route('profile.edit')" icon="cog">
                                {{ __('layout.settings') }}
                            </flux:menu.item>

                            <flux:menu.separator />

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                    {{ __('layout.logout') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </div>
        </header>

        <main class="min-h-[calc(100vh-5rem)] p-4 sm:p-6 lg:p-8">
            @if (request()->routeIs('dashboard'))
                {{ $slot }}
            @else
                <div class="mx-auto max-w-[1500px] rounded-[2rem] border border-white/80 bg-[#f8f9fc] p-4 shadow-2xl shadow-slate-400/20 sm:p-6 lg:p-8">
                    {{ $slot }}
                </div>
            @endif
        </main>

        @livewireScripts
        @fluxScripts
    </body>
</html>
