<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;

class TransactionIndex extends Component
{
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
        return view('livewire.transactions.transaction-index', [
            'transactions' => Transaction::where('user_id', auth()->id())
                ->orderByDesc('start_date')
                ->get(),
        ]);
    }
}
