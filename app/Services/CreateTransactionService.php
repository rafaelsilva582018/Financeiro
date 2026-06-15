<?php

namespace App\Services;

use App\Models\CreditCard;
use App\Models\Entry;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateTransactionService
{
    public function execute(array $data): Transaction
    {
        $this->validateFixedExpense($data);

        return DB::transaction(function () use ($data) {
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'type' => $data['type'],
                'description' => $data['description'],
                'total_value' => $data['total_value'],
                'start_date' => $data['start_date'],
                'is_fixed' => (bool) ($data['is_fixed'] ?? false),
                'installments' => $data['installments'] ?? null,
                'account_id' => $data['account_id'] ?? null,
                'credit_card_id' => $data['credit_card_id'] ?? null,
                'category_id' => $data['category_id'],
            ]);

            $this->generateEntries($transaction);

            return $transaction;
        });
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $this->validateFixedExpense($data);

        return DB::transaction(function () use ($transaction, $data) {
            $transaction->update($data);
            $transaction->entries()->delete();

            $this->generateEntries($transaction->refresh());

            return $transaction;
        });
    }

    private function validateFixedExpense(array $data): void
    {
        if (($data['is_fixed'] ?? false) !== true) {
            return;
        }

        if (! empty($data['installments'])) {
            throw new \Exception('Despesa fixa não pode ter parcelas.');
        }

        if (! empty($data['credit_card_id'])) {
            throw new \Exception('Despesa fixa não pode usar cartão de crédito.');
        }
    }

    private function generateEntries(Transaction $transaction): void
    {
        if ($transaction->credit_card_id) {
            $this->generateCreditCardEntries($transaction);

            return;
        }

        if ($transaction->is_fixed) {
            $this->generateFixedEntries($transaction);

            return;
        }

        $this->generateCashEntries($transaction);
    }

    private function generateFixedEntries(Transaction $transaction): void
    {
        $start = Carbon::parse($transaction->start_date)->startOfMonth();
        $dueDay = Carbon::parse($transaction->start_date)->day;

        for ($i = 0; $i < 12; $i++) {
            $referenceDate = $start->copy()->addMonths($i);

            Entry::create([
                'user_id' => $transaction->user_id,
                'transaction_id' => $transaction->id,
                'reference_date' => $referenceDate,
                'due_date' => $referenceDate->copy()->day(min($dueDay, $referenceDate->daysInMonth)),
                'value' => $transaction->total_value,
                'status' => 'pending',
                'account_id' => $transaction->account_id,
            ]);
        }
    }

    private function generateCashEntries(Transaction $transaction): void
    {
        Entry::create([
            'user_id' => $transaction->user_id,
            'transaction_id' => $transaction->id,
            'reference_date' => Carbon::parse($transaction->start_date)->startOfMonth(),
            'due_date' => Carbon::parse($transaction->start_date),
            'value' => $transaction->total_value,
            'status' => 'paid',
            'account_id' => $transaction->account_id,
        ]);
    }

    private function generateCreditCardEntries(Transaction $transaction): void
    {
        $card = CreditCard::where('user_id', $transaction->user_id)
            ->findOrFail($transaction->credit_card_id);

        $installments = max((int) ($transaction->installments ?? 1), 1);
        $totalCents = (int) round(((float) $transaction->total_value) * 100);
        $baseInstallmentCents = intdiv($totalCents, $installments);
        $remainderCents = $totalCents % $installments;
        $purchaseDate = Carbon::parse($transaction->start_date);
        $firstReference = $this->calculateFirstInvoiceMonth($purchaseDate, $card);

        for ($i = 0; $i < $installments; $i++) {
            $installmentCents = $baseInstallmentCents + ($i < $remainderCents ? 1 : 0);
            $referenceDate = $firstReference->copy()->addMonths($i);

            Entry::create([
                'user_id' => $transaction->user_id,
                'transaction_id' => $transaction->id,
                'reference_date' => $referenceDate,
                'due_date' => $this->dateInMonth($referenceDate, $card->due_day),
                'value' => $installmentCents / 100,
                'installment_number' => $i + 1,
                'installments_total' => $installments,
                'status' => 'pending',
                'credit_card_id' => $card->id,
            ]);
        }
    }

    private function calculateFirstInvoiceMonth(Carbon $purchaseDate, CreditCard $card): Carbon
    {
        $closingDate = $this->dateInMonth($purchaseDate, $card->closing_day);

        if ($purchaseDate->greaterThan($closingDate)) {
            return $purchaseDate->copy()->addMonth()->startOfMonth();
        }

        return $purchaseDate->copy()->startOfMonth();
    }

    private function dateInMonth(Carbon $date, int $day): Carbon
    {
        $month = $date->copy()->startOfMonth();

        return $month->day(min($day, $month->daysInMonth));
    }
}
