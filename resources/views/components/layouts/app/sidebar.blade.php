<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar
            sticky
            stashable
            class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
        >
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a
                href="{{ route('dashboard') }}"
                class="me-5 flex items-center space-x-2 rtl:space-x-reverse"
                wire:navigate
            >
                <x-app-logo />
            </a>

            {{-- Navegação principal --}}
            <flux:navlist variant="outline">

                {{-- PRINCIPAL --}}
                <flux:navlist.group heading="Principal">
                    <flux:navlist.item
                        icon="home"
                        :href="route('dashboard')"
                        :current="request()->routeIs('dashboard')"
                        wire:navigate
                    >
                        Dashboard
                    </flux:navlist.item>
                </flux:navlist.group>

                {{-- GESTÃO --}}
                <flux:navlist.group heading="Gestão">
                    <flux:navlist.item
                        icon="tag"
                        :href="route('categories.index')"
                        :current="request()->routeIs('categories.*')"
                        wire:navigate
                    >
                        Categorias
                    </flux:navlist.item>

                    <flux:navlist.item
                        icon="wallet"
                        :href="route('accounts.index')"
                        :current="request()->routeIs('accounts.*')"
                        wire:navigate
                    >
                        Contas
                    </flux:navlist.item>

                    <flux:navlist.item
                        icon="credit-card"
                        :href="route('credit-cards.index')"
                        :current="request()->routeIs('credit-cards.*')"
                        wire:navigate
                    >
                        Cartões de crédito
                    </flux:navlist.item>
                </flux:navlist.group>

                {{-- OPERAÇÕES --}}
                <flux:navlist.group heading="Operações">
                    <flux:navlist.item
                        icon="arrows-right-left"
                        :href="route('transactions.index')"
                        :current="request()->routeIs('transactions.*')"
                        wire:navigate
                    >
                        Transações
                    </flux:navlist.item>

                    <flux:navlist.item
                        icon="calendar-days"
                        :href="route('entries.index')"
                        :current="request()->routeIs('entries.*')"
                        wire:navigate
                    >
                        Lançamentos
                    </flux:navlist.item>

                    <flux:navlist.item
                        icon="building-library"
                        :href="route('bank-imports.index')"
                        :current="request()->routeIs('bank-imports.*')"
                        wire:navigate
                    >
                        Pendências bancárias
                    </flux:navlist.item>

                    <flux:navlist.item
                        icon="arrow-trending-up"
                        :href="route('reports.income')"
                        :current="request()->routeIs('reports.income')"
                        wire:navigate
                    >
                        Receitas
                    </flux:navlist.item>

                    <flux:navlist.item
                        icon="arrow-trending-down"
                        :href="route('reports.expenses')"
                        :current="request()->routeIs('reports.expenses')"
                        wire:navigate
                    >
                        Despesas
                    </flux:navlist.item>
                </flux:navlist.group>

            </flux:navlist>


            <flux:spacer />

            {{-- Menu do usuário (desktop) --}}
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>

                                <div class="grid flex-1 leading-tight">
                                    <span class="truncate font-semibold">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <span class="truncate text-xs">
                                        {{ auth()->user()->email }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item
                            :href="route('profile.edit')"
                            icon="cog"
                            wire:navigate
                        >
                            {{ __('layout.settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full"
                        >
                            {{ __('layout.logout') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        {{-- Menu mobile --}}
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>

                                <div class="grid flex-1 leading-tight">
                                    <span class="truncate font-semibold">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <span class="truncate text-xs">
                                        {{ auth()->user()->email }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.item
                        :href="route('profile.edit')"
                        icon="cog"
                        wire:navigate
                    >
                        {{ __('layout.settings') }}
                    </flux:menu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                        >
                            {{ __('layout.logout') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @include('components.chart-scripts')
        @livewireScripts
        @fluxScripts
    </body>
</html>
