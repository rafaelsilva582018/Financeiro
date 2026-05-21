# Finance Online

Finance Online é um sistema web para controle financeiro pessoal, criado com Laravel, Livewire, Flux UI e ApexCharts. A aplicação permite registrar receitas, despesas, contas, cartões de crédito, categorias e lançamentos, além de acompanhar tudo em um dashboard com gráficos e relatórios.

## Funcionalidades

- Dashboard financeiro com resumo do mês.
- Cards de saldo, receitas, despesas e pendências.
- Gráficos de receitas x despesas, composição do mês e categorias.
- Cadastro de transações em modal.
- Cadastro de contas bancárias e carteiras.
- Cadastro de cartões de crédito com limite, fechamento e vencimento.
- Cadastro de categorias para receitas e despesas.
- Controle de lançamentos pagos e pendentes.
- Relatórios separados de receitas e despesas.
- Comparação dos últimos meses com gráficos.
- Máscara de moeda no padrão brasileiro.
- Interface em português.
- Autenticação de usuários com Laravel Fortify.

## Tecnologias

- PHP 8.2+
- Laravel 12
- Livewire
- Flux UI
- Alpine.js
- Tailwind CSS
- ApexCharts
- Vite
- Pest / PHPUnit

## Requisitos

Antes de rodar o projeto, tenha instalado:

- PHP 8.2 ou superior
- Composer
- Node.js e npm
- MySQL, MariaDB ou SQLite

## Instalação

Clone o repositório:

```bash
git clone https://github.com/rafaelsilva582018/Financeiro.git
cd Financeiro
```

Instale as dependências do PHP:

```bash
composer install
```

Instale as dependências do JavaScript:

```bash
npm install
```

Crie o arquivo de ambiente:

```bash
cp .env.example .env
```

No Windows PowerShell, se preferir:

```powershell
copy .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

Configure o banco de dados no arquivo `.env`.

Depois rode as migrations:

```bash
php artisan migrate
```

## Rodando o projeto

Em um terminal, inicie o Laravel:

```bash
php artisan serve
```

Em outro terminal, inicie o Vite:

```bash
npm run dev
```

Acesse no navegador:

```txt
http://127.0.0.1:8000
```

## Build de produção

Para gerar os arquivos finais do frontend:

```bash
npm run build
```

## Testes

Para rodar os testes:

```bash
php artisan test
```

Ou usando o script do Composer:

```bash
composer test
```

## Estrutura principal

```txt
app/Livewire              Componentes Livewire
app/Services              Regras de negócio
resources/views/livewire  Telas da aplicação
resources/js              JavaScript principal
resources/css             Estilos
routes/web.php            Rotas web
database/migrations       Estrutura do banco de dados
tests                     Testes automatizados
```

## Rotas principais

- `/dashboard` - Dashboard principal
- `/transactions` - Transações
- `/entries` - Lançamentos
- `/categories` - Categorias
- `/accounts` - Contas
- `/credit-cards` - Cartões de crédito
- `/reports/receitas` - Relatório de receitas
- `/reports/despesas` - Relatório de despesas

## Observações

O sistema foi pensado para uso pessoal e organização financeira mensal. Os gráficos são carregados com ApexCharts e funcionam tanto no carregamento normal da página quanto na navegação interna do Livewire.

## Licença

Este projeto utiliza a base do Laravel, distribuída sob a licença MIT.