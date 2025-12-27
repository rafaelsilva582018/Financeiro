<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Category;
use App\Models\Account;
use App\Models\CreditCard;
use App\Models\Transaction;
use App\Models\Entry;

class FinanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $this->command->error('Nenhum usuário encontrado.');
            return;
        }

        /** 🏷️ Categorias */
        $incomeCategories = [
            'Salário',
            'Freelance',
            'Rendimentos',
        ];

        $expenseCategories = [
            'Aluguel',
            'Mercado',
            'Transporte',
            'Lazer',
            'Internet',
            'Cartão de crédito',
        ];

        foreach ($incomeCategories as $name) {
            Category::firstOrCreate([
                'user_id' => $user->id,
                'name' => $name,
                'type' => 'income',
            ]);
        }

        foreach ($expenseCategories as $name) {
            Category::firstOrCreate([
                'user_id' => $user->id,
                'name' => $name,
                'type' => 'expense',
            ]);
        }

        /** 🏦 Conta */
        $account = Account::firstOrCreate([
            'user_id' => $user->id,
            'name' => 'Conta Principal',
        ], [
            'initial_balance' => 2500,
        ]);

        /** 💳 Cartão */
        $card = CreditCard::firstOrCreate([
            'user_id' => $user->id,
            'name' => 'Nubank',
        ], [
            'limit' => 5000,
            'closing_day' => 20,
            'due_day' => 28,
        ]);

        /** 📅 Criar transações e lançamentos */
        $start = now()->subMonths(4)->startOfMonth();

        for ($m = 0; $m <= 4; $m++) {
            $date = $start->copy()->addMonths($m);

            /** 💰 Receita */
            $salaryCategory = Category::where('name', 'Salário')->first();

            $salary = Transaction::create([
                'user_id' => $user->id,
                'type' => 'income',
                'description' => 'Salário mensal',
                'category_id' => $salaryCategory->id,
                'total_value' => 5000,
                'start_date' => $date,
            ]);

            Entry::create([
                'user_id' => $user->id,
                'transaction_id' => $salary->id,
                'value' => 5000,
                'reference_date' => $date,
                'status' => 'paid',
            ]);

            /** 🏠 Aluguel */
            $rentCategory = Category::where('name', 'Aluguel')->first();

            $rent = Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'description' => 'Aluguel',
                'category_id' => $rentCategory->id,
                'total_value' => 1500,
                'start_date' => $date,
            ]);

            Entry::create([
                'user_id' => $user->id,
                'transaction_id' => $rent->id,
                'value' => 1500,
                'reference_date' => $date,
                'status' => 'paid',
            ]);

            /** 🛒 Mercado */
            $marketCategory = Category::where('name', 'Mercado')->first();

            $market = Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'description' => 'Supermercado',
                'category_id' => $marketCategory->id,
                'total_value' => 800,
                'start_date' => $date,
            ]);

            Entry::create([
                'user_id' => $user->id,
                'transaction_id' => $market->id,
                'value' => 800,
                'reference_date' => $date,
                'status' => 'paid',
            ]);

            /** 💳 Compra no cartão */
            $cardCategory = Category::where('name', 'Cartão de crédito')->first();

            $credit = Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'description' => 'Compras no cartão',
                'category_id' => $cardCategory->id,
                'total_value' => 1200,
                'start_date' => $date,
                'credit_card_id' => $card->id,
            ]);

            Entry::create([
                'user_id' => $user->id,
                'transaction_id' => $credit->id,
                'credit_card_id' => $card->id,
                'value' => 1200,
                'reference_date' => $date,
                'status' => 'paid',
            ]);
        }

        $this->command->info('✅ Dados fictícios criados com sucesso!');
    }
}
