<?php

namespace App\Livewire\Entries;

use Livewire\Component;
use App\Models\Entry;

class EntryIndex extends Component
{
    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    /*
    |--------------------------------------------------------------------------
    | Alternar status
    |--------------------------------------------------------------------------
    */
    public function toggleStatus(int $id): void
    {
        $entry = Entry::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereHas('transaction') // 🔒 garante que não é órfã
            ->firstOrFail();

        $entry->update([
            'status' => $entry->status === 'paid' ? 'pending' : 'paid',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Lista de lançamentos do mês
    |--------------------------------------------------------------------------
    */
    public function getEntriesProperty()
    {
        [$year, $month] = explode('-', $this->month);

        return Entry::where('user_id', auth()->id())
            ->whereYear('reference_date', $year)
            ->whereMonth('reference_date', $month)

            ->whereHas('transaction')   // 🔥 remove órfãos
            ->with(['transaction.category', 'account', 'creditCard'])       // 🚀 evita N+1 query

            ->orderBy('reference_date')
            ->orderBy('status')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        return view('livewire.entries.entry-index', [
            'entries' => $this->entries,
        ]);
    }
}
