<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\Entry;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateFixedEntriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Executado pelo scheduler
     */
    public function handle(): void
    {
        $transactions = Transaction::where('is_fixed', true)
            ->get();

        foreach ($transactions as $transaction) {

            // Mês atual
            $this->ensureEntryExists(
                $transaction,
                now()->startOfMonth()
            );

            // Próximo mês
            $this->ensureEntryExists(
                $transaction,
                now()->addMonth()->startOfMonth()
            );
        }
    }

    /**
     * Garante que o lançamento exista sem duplicar
     */
    private function ensureEntryExists(
        Transaction $transaction,
        Carbon $referenceDate
    ): void {
        $exists = Entry::where('transaction_id', $transaction->id)
            ->whereDate('reference_date', $referenceDate->toDateString())
            ->exists();

        if ($exists) {
            return;
        }

        Entry::create([
            'user_id'         => $transaction->user_id,
            'transaction_id' => $transaction->id,
            'reference_date' => $referenceDate,
            'due_date'        => $referenceDate->copy()->day(
                min(Carbon::parse($transaction->start_date)->day, $referenceDate->daysInMonth)
            ),
            'value'           => $transaction->total_value,
            'status'          => 'pending',
            'account_id'      => $transaction->account_id,
        ]);
    }
}
