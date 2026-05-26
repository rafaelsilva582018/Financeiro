<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use Livewire\Component;

class TransactionIndex extends Component
{
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
        return view('livewire.transactions.transaction-index', [
            'transactions' => Transaction::where('user_id', auth()->id())
                ->with(['category', 'account', 'creditCard'])
                ->orderByDesc('start_date')
                ->get(),
        ]);
    }
}