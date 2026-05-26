<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased">
        <main class="grid min-h-svh lg:grid-cols-[1.05fr_0.95fr]">
            <section class="relative hidden overflow-hidden bg-zinc-950 px-10 py-10 lg:flex lg:flex-col">
                <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(16,185,129,0.16),transparent_38%),linear-gradient(45deg,rgba(99,102,241,0.14),transparent_42%)]"></div>
                <div class="absolute inset-x-10 bottom-0 h-px bg-gradient-to-r from-transparent via-emerald-300/60 to-transparent"></div>

                <a href="{{ route('home') }}" class="relative z-10 flex items-center gap-3 font-semibold" wire:navigate>
                    <x-app-logo-icon class="size-11" />
                    <span>Finance Online</span>
                </a>

                <div class="relative z-10 my-auto max-w-xl">
                    <p class="text-sm font-semibold uppercase text-emerald-300">Gestao financeira</p>
                    <h1 class="mt-5 text-4xl font-semibold leading-tight text-white">Controle seus numeros com clareza, rotina e previsibilidade.</h1>
                    <p class="mt-5 max-w-lg text-base leading-7 text-zinc-300">Acompanhe contas, cartoes, despesas e receitas em um ambiente preparado para decisao diaria.</p>

                    <div class="mt-10 grid max-w-md grid-cols-2 gap-3">
                        <div class="rounded-lg border border-white/10 bg-white/8 p-4">
                            <p class="text-xs text-zinc-400">Saldo previsto</p>
                            <p class="mt-2 text-2xl font-semibold text-white">R$ 8.420</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-white/8 p-4">
                            <p class="text-xs text-zinc-400">Economia mensal</p>
                            <p class="mt-2 text-2xl font-semibold text-emerald-300">18%</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="flex min-h-svh items-center justify-center bg-zinc-50 px-5 py-8 text-zinc-950 dark:bg-zinc-950 dark:text-white sm:px-8">
                <div class="w-full max-w-md">
                    <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-3 font-semibold lg:hidden" wire:navigate>
                        <x-app-logo-icon class="size-11" />
                        <span>Finance Online</span>
                    </a>

                    <div class="rounded-lg border border-zinc-200 bg-white px-6 py-7 shadow-xl shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 sm:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </section>
        </main>

        @fluxScripts
    </body>
</html>
