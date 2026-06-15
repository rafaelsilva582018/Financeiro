<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Finance Online - Controle financeiro profissional</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Plataforma para organizar receitas, despesas, contas, cartoes e relatorios financeiros em um unico lugar.">

    <script>
        (() => {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-zinc-50 text-zinc-950 antialiased dark:bg-zinc-950 dark:text-white">
    <header class="fixed inset-x-0 top-0 z-40 border-b border-white/10 bg-zinc-950/88 text-white backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold">
                <x-app-logo-icon class="size-10" />
                <span>Finance Online</span>
            </a>

            <div class="flex items-center gap-2 sm:gap-4">
                <button
                    x-data
                    @click="
                        document.documentElement.classList.toggle('dark');
                        localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                    "
                    class="inline-flex size-10 items-center justify-center rounded-lg border border-white/10 text-sm text-zinc-300 transition hover:bg-white/10"
                    aria-label="Alternar tema"
                >
                    <span x-show="!document.documentElement.classList.contains('dark')">N</span>
                    <span x-show="document.documentElement.classList.contains('dark')">D</span>
                </button>

                <nav class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="hidden rounded-lg px-4 py-2 text-sm font-semibold text-zinc-300 transition hover:bg-white/10 hover:text-white sm:inline-flex">Entrar</a>
                    <a href="{{ route('register') }}" class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-400 px-4 text-sm font-semibold text-zinc-950 shadow-sm transition hover:bg-emerald-300">Criar conta</a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <section class="relative min-h-[92svh] overflow-hidden bg-zinc-950 pt-24 text-white">
            <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(16,185,129,0.18),transparent_38%),linear-gradient(45deg,rgba(99,102,241,0.14),transparent_42%),linear-gradient(180deg,rgba(9,9,11,0.1),#09090b_92%)]"></div>

            <div class="absolute inset-x-4 bottom-0 top-28 mx-auto max-w-6xl opacity-70 sm:inset-x-8">
                <div class="h-full rounded-t-2xl border border-white/10 bg-white/[0.04] p-3 shadow-2xl shadow-emerald-950/30">
                    <div class="grid h-full gap-3 lg:grid-cols-[0.7fr_1fr]">
                        <div class="hidden rounded-lg border border-white/10 bg-zinc-900/90 p-4 lg:block">
                            <div class="mb-5 flex items-center gap-2">
                                <x-app-logo-icon class="size-8" />
                                <span class="text-sm font-semibold">Finance Online</span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-9 rounded-lg bg-emerald-400/16"></div>
                                <div class="h-9 rounded-lg bg-white/8"></div>
                                <div class="h-9 rounded-lg bg-white/8"></div>
                                <div class="h-9 rounded-lg bg-white/8"></div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-white/10 bg-zinc-900/90 p-4 sm:p-6">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <div class="h-3 w-28 rounded-full bg-white/16"></div>
                                    <div class="mt-3 h-8 w-48 rounded-full bg-white/12"></div>
                                </div>
                                <div class="h-10 w-28 rounded-lg bg-emerald-400/90"></div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="rounded-lg border border-white/10 bg-white/8 p-4">
                                    <div class="h-3 w-20 rounded-full bg-white/18"></div>
                                    <div class="mt-6 h-7 w-28 rounded-full bg-emerald-300/80"></div>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/8 p-4">
                                    <div class="h-3 w-20 rounded-full bg-white/18"></div>
                                    <div class="mt-6 h-7 w-24 rounded-full bg-rose-300/80"></div>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/8 p-4">
                                    <div class="h-3 w-20 rounded-full bg-white/18"></div>
                                    <div class="mt-6 h-7 w-20 rounded-full bg-indigo-300/80"></div>
                                </div>
                            </div>

                            <div class="mt-3 grid gap-3 lg:grid-cols-[1fr_0.65fr]">
                                <div class="rounded-lg border border-white/10 bg-white/8 p-4">
                                    <div class="flex h-44 items-end gap-2">
                                        <span class="h-16 flex-1 rounded-t bg-emerald-300/80"></span>
                                        <span class="h-28 flex-1 rounded-t bg-indigo-300/80"></span>
                                        <span class="h-20 flex-1 rounded-t bg-emerald-300/80"></span>
                                        <span class="h-36 flex-1 rounded-t bg-indigo-300/80"></span>
                                        <span class="h-24 flex-1 rounded-t bg-emerald-300/80"></span>
                                        <span class="h-40 flex-1 rounded-t bg-indigo-300/80"></span>
                                    </div>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/8 p-4">
                                    <div class="mx-auto mt-3 size-32 rounded-full border-[18px] border-emerald-300/80 border-r-indigo-300/80 border-t-rose-300/80"></div>
                                    <div class="mx-auto mt-6 h-3 w-28 rounded-full bg-white/14"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mx-auto flex min-h-[calc(92svh-6rem)] max-w-7xl items-center px-5 py-16 sm:px-6">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase text-emerald-300">Controle financeiro inteligente</p>
                    <h1 class="mt-5 max-w-3xl text-4xl font-semibold leading-tight text-white sm:text-6xl">Finance Online</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-zinc-300">Organize receitas, despesas, contas e cartoes com uma visao clara do mes. Menos planilha, mais decisao.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex h-12 items-center justify-center rounded-lg bg-emerald-400 px-6 text-sm font-semibold text-zinc-950 shadow-lg shadow-emerald-950/30 transition hover:bg-emerald-300">Comecar agora</a>
                        <a href="{{ route('login') }}" class="inline-flex h-12 items-center justify-center rounded-lg border border-white/14 bg-white/8 px-6 text-sm font-semibold text-white transition hover:bg-white/14">Acessar minha conta</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-zinc-50 py-20 dark:bg-zinc-950">
            <div class="mx-auto max-w-7xl px-5 sm:px-6">
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ([
                        ['Contas e caixa', 'Acompanhe saldo inicial, entradas, saidas e previsao mensal.'],
                        ['Cartoes e parcelas', 'Veja faturas, limites e compras parceladas no periodo certo.'],
                        ['Relatorios claros', 'Compare categorias e entenda para onde o dinheiro esta indo.'],
                    ] as [$title, $description])
                        <article class="rounded-lg border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <div class="mb-5 h-1.5 w-12 rounded-full bg-emerald-400"></div>
                            <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ $title }}</h2>
                            <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="border-y border-zinc-200 bg-white py-16 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mx-auto grid max-w-7xl gap-10 px-5 sm:px-6 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase text-emerald-600 dark:text-emerald-400">Rotina financeira</p>
                    <h2 class="mt-4 text-3xl font-semibold leading-tight text-zinc-950 dark:text-white">Uma visao unica para o que ja foi pago, o que esta pendente e o que vem pela frente.</h2>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg bg-zinc-950 p-5 text-white dark:bg-white dark:text-zinc-950">
                        <p class="text-sm opacity-70">Resumo mensal</p>
                        <p class="mt-5 text-2xl font-semibold">R$ 12.480</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 p-5 dark:border-zinc-800">
                        <p class="text-sm text-zinc-500">Despesas</p>
                        <p class="mt-5 text-2xl font-semibold text-rose-600">R$ 4.210</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 p-5 dark:border-zinc-800">
                        <p class="text-sm text-zinc-500">Receitas</p>
                        <p class="mt-5 text-2xl font-semibold text-emerald-600">R$ 8.270</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-zinc-950 text-zinc-400">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-8 text-sm sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-center gap-3">
                <x-app-logo-icon class="size-8" />
                <span>Finance Online</span>
            </div>
            <span>&copy; {{ date('Y') }} Finance Online. Todos os direitos reservados.</span>
        </div>
    </footer>
</body>
</html>
