<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Finance Online — Controle financeiro inteligente</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="Sistema profissional para controle financeiro pessoal. Gerencie receitas, despesas, cartões e acompanhe sua evolução com gráficos claros.">

    {{-- Evita flash branco --}}
    <script>
        (() => {
            const theme = localStorage.getItem('theme');
            if (
                theme === 'dark' ||
                (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)
            ) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-white dark:bg-zinc-950 text-gray-800 dark:text-gray-200 antialiased">

{{-- =========================
|  HEADER
========================= --}}
<header class="border-b bg-white dark:bg-zinc-900 dark:border-zinc-800">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        {{-- LOGO --}}
        <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-bold text-lg">
            Finance Online
        </div>

        <div class="flex items-center gap-4">

            {{-- BOTÃO DARK MODE --}}
            <button
                x-data
                @click="
                    document.documentElement.classList.toggle('dark');
                    localStorage.setItem(
                        'theme',
                        document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                    );
                "
                class="rounded-lg p-2
                       text-gray-500 hover:bg-gray-100
                       dark:text-gray-400 dark:hover:bg-zinc-800
                       transition"
                aria-label="Alternar tema"
            >
                <span x-show="!document.documentElement.classList.contains('dark')">🌙</span>
                <span x-show="document.documentElement.classList.contains('dark')">☀️</span>
            </button>

            {{-- LINKS --}}
            <nav class="flex items-center gap-4">
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-gray-600 dark:text-gray-300
                          hover:text-gray-900 dark:hover:text-white transition">
                    Entrar
                </a>

                <a href="{{ route('register') }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg
                          bg-indigo-600 text-white text-sm font-semibold
                          hover:bg-indigo-500 transition">
                    Criar conta
                </a>
            </nav>
        </div>
    </div>
</header>

{{-- =========================
|  HERO
========================= --}}
<section class="relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 py-24 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">

        <div>
            <span class="inline-block mb-4 text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                Controle financeiro inteligente
            </span>

            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6">
                Organize seu dinheiro.
                <br>
                <span class="text-indigo-600 dark:text-indigo-400">
                    Tenha clareza financeira.
                </span>
            </h1>

            <p class="text-gray-600 dark:text-gray-400 text-lg mb-8 max-w-xl">
                Gerencie receitas, despesas, cartões de crédito e acompanhe sua
                evolução financeira com gráficos claros e decisões baseadas em dados.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('register') }}"
                   class="inline-flex justify-center px-6 py-3 rounded-lg
                          bg-indigo-600 text-white font-semibold
                          hover:bg-indigo-500 transition">
                    Começar gratuitamente
                </a>

                <a href="{{ route('login') }}"
                   class="inline-flex justify-center px-6 py-3 rounded-lg
                          border border-gray-300 dark:border-zinc-700
                          font-semibold text-gray-700 dark:text-gray-200
                          hover:bg-gray-50 dark:hover:bg-zinc-800 transition">
                    Já tenho conta
                </a>
            </div>
        </div>

        {{-- Visual --}}
        <div class="relative bg-indigo-50 dark:bg-zinc-800 rounded-3xl p-12 text-center shadow-inner">
            <div class="text-6xl mb-6">📊</div>
            <p class="text-gray-700 dark:text-gray-300 font-medium">
                Visualização clara dos seus gastos, receitas
                e evolução mensal em um único lugar.
            </p>
        </div>
    </div>
</section>

{{-- =========================
|  FEATURES
========================= --}}
<section class="bg-gray-50 dark:bg-zinc-900 py-24">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-14">
            Tudo o que você precisa para controlar suas finanças
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            @foreach ([
                ['💵','Receitas e despesas','Controle total de entradas e saídas com organização mensal.'],
                ['💳','Cartões de crédito','Acompanhe faturas, limites e parcelas sem surpresas.'],
                ['📈','Relatórios e gráficos','Tome decisões melhores com visualizações claras e objetivas.']
            ] as [$icon,$title,$desc])
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border
                            border-gray-200 dark:border-zinc-700
                            p-8 text-center shadow-sm">
                    <div class="text-4xl mb-4">{{ $icon }}</div>
                    <h3 class="font-semibold text-lg mb-2">{{ $title }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        {{ $desc }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- =========================
|  CTA FINAL
========================= --}}
<section class="bg-indigo-600 py-20">
    <div class="max-w-3xl mx-auto px-6 text-center text-white">
        <h2 class="text-3xl font-bold mb-6">
            Comece agora a organizar sua vida financeira
        </h2>

        <p class="mb-8 text-indigo-100">
            Sem complicação. Sem planilhas. Tudo em um só lugar.
        </p>

        <a href="{{ route('register') }}"
           class="inline-flex px-8 py-4 rounded-lg
                  bg-white text-indigo-600 font-semibold
                  hover:bg-indigo-50 transition">
            Criar conta gratuita
        </a>
    </div>
</section>

{{-- =========================
|  FOOTER
========================= --}}
<footer class="border-t bg-white dark:bg-zinc-900 dark:border-zinc-800">
    <div class="max-w-7xl mx-auto px-6 py-8
                flex flex-col md:flex-row items-center justify-between gap-4
                text-sm text-gray-500 dark:text-gray-400">

        <span>
            © {{ date('Y') }} Finance Online. Todos os direitos reservados.
        </span>

        <div class="flex gap-4">
            <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200">Termos</a>
            <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200">Privacidade</a>
            <a href="#" class="hover:text-gray-700 dark:hover:text-gray-200">Contato</a>
        </div>
    </div>
</footer>

</body>
</html>
