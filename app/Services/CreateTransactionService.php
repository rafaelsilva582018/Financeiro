<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Entry;
use App\Models\CreditCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateTransactionService
{
    /**
     * Método principal chamado pelo controller / livewire
     */
    public function execute(array $data): Transaction
    {
        // ✅ Validações de negócio
        $this->validateFixedExpense($data);

        return DB::transaction(function () use ($data) {

            $transaction = Transaction::create([
                'user_id'        => auth()->id(),
                'type'           => $data['type'], // income | expense
                'description'    => $data['description'],
                'total_value'    => $data['total_value'],
                'start_date'     => $data['start_date'],
                'is_fixed'       => (bool) ($data['is_fixed'] ?? false),
                'installments'   => $data['installments'] ?? null,
                'account_id'     => $data['account_id'] ?? null,
                'credit_card_id' => $data['credit_card_id'] ?? null,
                'category_id'    => $data['category_id'],
            ]);

            // 🔁 Geração dos lançamentos
            if ($transaction->credit_card_id) {
                $this->generateCreditCardEntries($transaction);
            } elseif ($transaction->is_fixed) {
                $this->generateFixedEntries($transaction);
            } else {
                $this->generateCashEntries($transaction);
            }

            return $transaction;
        });
    }

    /* =====================================================
     |  Validações de negócio
     ===================================================== */

    private function validateFixedExpense(array $data): void
    {
        if (($data['is_fixed'] ?? false) === true) {

            if (! empty($data['installments'])) {
                throw new \Exception(
                    'Despesa fixa não pode ter parcelas.'
                );
            }

            if (! empty($data['credit_card_id'])) {
                throw new \Exception(
                    'Despesa fixa não pode usar cartão de crédito.'
                );
            }
        }
    }

    /* =====================================================
     |  Geração de lançamentos
     ===================================================== */

    /**
     * 🔁 Despesa / Receita fixa (mensal)
     */
    private function generateFixedEntries(Transaction $transaction): void
    {
        $start = Carbon::parse($transaction->start_date)
            ->startOfMonth();

        $months = 12; // Pode virar config ou end_date depois

        for ($i = 0; $i < $months; $i++) {
            Entry::create([
                'user_id'         => $transaction->user_id,
                'transaction_id' => $transaction->id,
                'reference_date' => $start->copy()->addMonths($i),
                'value'           => $transaction->total_value,
                'status'          => 'pending',
                'account_id'      => $transaction->account_id,
            ]);
        }
    }

    /**
     * 💰 À vista (conta bancária) ou receita simples
     */
    private function generateCashEntries(Transaction $transaction): void
    {
        Entry::create([
            'user_id'         => $transaction->user_id,
            'transaction_id' => $transaction->id,
            'reference_date' => Carbon::parse(
                $transaction->start_date
            )->startOfMonth(),
            'value'           => $transaction->total_value,
            'status'          => 'pending',
            'account_id'      => $transaction->account_id,
        ]);
    }

    /**
     * 💳 Parcelado no cartão de crédito
     */
    private function generateCreditCardEntries(
        Transaction $transaction
    ): void {
        $card = CreditCard::findOrFail(
            $transaction->credit_card_id
        );

        $installments = $transaction->installments ?? 1;

        $valuePerInstallment = round(
            $transaction->total_value / $installments,
            2
        );

        $purchaseDate = Carbon::parse($transaction->start_date);

        $firstReference = $this->calculateFirstInvoiceMonth(
            $purchaseDate,
            $card
        );

        for ($i = 0; $i < $installments; $i++) {
            Entry::create([
                'user_id'         => $transaction->user_id,
                'transaction_id' => $transaction->id,
                'reference_date' => $firstReference->copy()
                    ->addMonths($i),
                'value'           => $valuePerInstallment,
                'status'          => 'pending',
                'credit_card_id'  => $card->id,
            ]);
        }
    }

    /**
     * 📅 Define em qual fatura a compra entra
     */
    private function calculateFirstInvoiceMonth(
        Carbon $purchaseDate,
        CreditCard $card
    ): Carbon {
        $closingDate = $purchaseDate->copy()
            ->day($card->closing_day);

        if ($purchaseDate->greaterThan($closingDate)) {
            return $purchaseDate->addMonth()->startOfMonth();
        }

        return $purchaseDate->startOfMonth();
    }
}
