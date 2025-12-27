<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Entry;
use Carbon\Carbon;

class MonthlyBalanceService
{
    /**
     * Retorna o saldo final de um mês
     */
    public function getBalanceForMonth(
        int $userId,
        Carbon $month
    ): float {
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd   = $month->copy()->endOfMonth();

        // 1️⃣ Saldo inicial das contas
        $initialBalance = Account::where('user_id', $userId)
            ->sum('initial_balance');

        // 2️⃣ Receitas pagas no mês (não cartão)
        $income = Entry::where('user_id', $userId)
            ->whereBetween('reference_date', [$monthStart, $monthEnd])
            ->where('status', 'paid')
            ->whereHas('transaction', function ($q) {
                $q->where('type', 'income');
            })
            ->sum('value');

        // 3️⃣ Despesas pagas no mês (não cartão)
        $expenses = Entry::where('user_id', $userId)
            ->whereBetween('reference_date', [$monthStart, $monthEnd])
            ->where('status', 'paid')
            ->whereHas('transaction', function ($q) {
                $q->where('type', 'expense');
            })
            ->whereNull('credit_card_id')
            ->sum('value');

        return ($initialBalance + $income) - $expenses;
    }
}
