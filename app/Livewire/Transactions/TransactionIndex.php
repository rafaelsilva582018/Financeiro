<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionIndex extends Component
{
    use WithPagination;

    protected $listeners = ['transaction-created' => '$refresh'];

    public function delete(int $id): void
    {
        $transaction = Transaction::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $transaction->delete();

        session()->flash('success', 'Transação excluída com sucesso.');
    }

    public function render()
    {
        $baseQuery = Transaction::where('user_id', auth()->id());

        return view('livewire.transactions.transaction-index', [
            'transactions' => (clone $baseQuery)
                ->with(['category', 'account', 'creditCard'])
                ->orderByDesc('start_date')
                ->orderByDesc('id')
                ->paginate(12),
            'summary' => [
                'count' => (clone $baseQuery)->count(),
                'income' => (float) (clone $baseQuery)->where('type', 'income')->sum('total_value'),
                'expenses' => (float) (clone $baseQuery)->where('type', 'expense')->sum('total_value'),
                'fixed_count' => (clone $baseQuery)->where('is_fixed', true)->count(),
            ],
        ]);
    }
}
