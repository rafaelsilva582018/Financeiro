<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-white dark:bg-zinc-800">
        {{-- Header --}}
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a
                href="{{ route('dashboard') }}"
                class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
                wire:navigate
            >
                <x-app-logo />
            </a>

            {{-- Navbar desktop --}}
            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item
                    icon="layout-grid"
                    :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                    wire:navigate
                >
                    {{ __('layout.dashboard') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            {{-- Ações rápidas --}}
            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('common.search')" position="bottom">
                    <flux:navbar.item
                        class="!h-10 [&>div>svg]:size-5"
                        icon="magnifying-glass"
                        href="#"
                        :label="__('common.search')"
                    />
                </flux:tooltip>
            </flux:navbar>

            {{-- Menu do usuário --}}
            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
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
                            class="w-full"
                        >
                            {{ __('layout.logout') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{-- Sidebar mobile --}}
        <flux:sidebar
            stashable
            sticky
            class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900"
        >
            <flux:sidebar.toggle icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('layout.platform')">
                    <flux:navlist.item
                        icon="layout-grid"
                        :href="route('dashboard')"
                        :current="request()->routeIs('dashboard')"
                        wire:navigate
                    >
                        {{ __('layout.dashboard') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>
        </flux:sidebar>

        {{ $slot }}

        @livewireScripts
        @fluxScripts
    </body>
</html>
