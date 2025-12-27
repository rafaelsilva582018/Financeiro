<?php

namespace App\Livewire\Entries;

use Livewire\Component;
use App\Models\Entry;
use Carbon\Carbon;

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
            ->firstOrFail();

        $entry->update([
            'status' => $entry->status === 'paid'
                ? 'pending'
                : 'paid',
        ]);
    }

    public function getEntriesProperty()
    {
        [$year, $month] = explode('-', $this->month);

        return Entry::where('user_id', auth()->id())
            ->whereYear('reference_date', $year)
            ->whereMonth('reference_date', $month)
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
