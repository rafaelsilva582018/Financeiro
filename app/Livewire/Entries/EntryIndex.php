<?php

namespace App\Livewire\Entries;

use App\Models\Entry;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;

class EntryIndex extends Component
{
    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function toggleStatus(int $id): void
    {
        $entry = Entry::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereHas('transaction')
            ->firstOrFail();

        $entry->update([
            'status' => $entry->status === 'paid' ? 'pending' : 'paid',
        ]);
    }

    public function getEntriesProperty()
    {
        [$year, $month] = explode('-', $this->month);
        $referenceDate = Carbon::create((int) $year, (int) $month, 1)->startOfMonth();
        $monthStart = $referenceDate->toDateString();
        $monthEnd = $referenceDate->copy()->endOfMonth()->toDateString();

        $this->ensureFixedEntriesForMonth($referenceDate);

        return Entry::where('user_id', auth()->id())
            ->whereHas('transaction')
            ->whereBetween('reference_date', [$monthStart, $monthEnd])
            ->with(['transaction.category', 'account', 'creditCard'])
            ->orderBy('reference_date')
            ->orderBy('status')
            ->get();
    }

    private function ensureFixedEntriesForMonth(Carbon $referenceDate): void
    {
        Transaction::where('user_id', auth()->id())
            ->where('is_fixed', true)
            ->whereDate('start_date', '<=', $referenceDate->copy()->endOfMonth())
            ->get()
            ->each(function (Transaction $transaction) use ($referenceDate) {
                Entry::query()->insertOrIgnore([
                    'user_id' => $transaction->user_id,
                    'transaction_id' => $transaction->id,
                    'reference_date' => $referenceDate->copy()->startOfDay(),
                    'due_date' => $this->fixedDueDate($transaction, $referenceDate),
                    'value' => $transaction->total_value,
                    'status' => 'pending',
                    'account_id' => $transaction->account_id,
                    'credit_card_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    private function fixedDueDate(Transaction $transaction, Carbon $referenceDate): Carbon
    {
        $startDate = Carbon::parse($transaction->start_date);

        return $referenceDate->copy()->day(min($startDate->day, $referenceDate->daysInMonth));
    }

    public function render()
    {
        return view('livewire.entries.entry-index', [
            'entries' => $this->entries,
        ]);
    }
}
