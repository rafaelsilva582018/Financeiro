<?php

namespace App\Livewire\Entries;

use App\Models\Entry;
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

        return Entry::where('user_id', auth()->id())
            ->whereHas('transaction')
            ->where(function ($query) use ($year, $month) {
                $query
                    ->where(function ($referenceQuery) use ($year, $month) {
                        $referenceQuery
                            ->whereYear('reference_date', $year)
                            ->whereMonth('reference_date', $month);
                    })
                    ->orWhere(function ($purchaseQuery) use ($year, $month) {
                        $purchaseQuery
                            ->where('entries.id', function ($firstEntryQuery) {
                                $firstEntryQuery
                                    ->select('first_entry.id')
                                    ->from('entries as first_entry')
                                    ->whereColumn('first_entry.transaction_id', 'entries.transaction_id')
                                    ->orderBy('first_entry.reference_date')
                                    ->orderBy('first_entry.id')
                                    ->limit(1);
                            })
                            ->whereHas('transaction', function ($transactionQuery) use ($year, $month) {
                                $transactionQuery
                                    ->whereYear('start_date', $year)
                                    ->whereMonth('start_date', $month);
                            });
                    });
            })
            ->with(['transaction.category', 'account', 'creditCard'])
            ->orderBy('reference_date')
            ->orderBy('status')
            ->get();
    }

    public function render()
    {
        return view('livewire.entries.entry-index', [
            'entries' => $this->entries,
        ]);
    }
}
