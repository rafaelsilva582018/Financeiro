<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Entry;
use Carbon\Carbon;

class MonthlySummaryService
{
    public function getSummary(int $userId, Carbon $reference): array
    {
        $start = $reference->copy()->startOfMonth();
        $end   = $reference->copy()->endOfMonth();

        $initialBalance = Account::where('user_id', $userId)
            ->sum('initial_balance');

        $income = Entry::where('user_id', $userId)
            ->whereBetween('reference_date', [$start, $end])
            ->where('status', 'paid')
            ->whereHas('transaction', fn ($q) => $q->where('type', 'income'))
            ->sum('value');

        $expenses = Entry::where('user_id', $userId)
            ->whereBetween('reference_date', [$start, $end])
            ->where('status', 'paid')
            ->whereHas('transaction', fn ($q) => $q->where('type', 'expense'))
            ->sum('value');

        return [
            'initial_balance' => (float) $initialBalance,
            'income' => (float) $income,
            'expenses' => (float) $expenses,
            'final_balance' => (float) ($initialBalance + $income - $expenses),
        ];
    }
}
