<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;

class TransactionIndex extends Component
{
    public function render()
    {
        return view('livewire.transactions.transaction-index', [
            'transactions' => Transaction::where('user_id', auth()->id())
                ->orderByDesc('start_date')
                ->get(),
        ]);
    }
}
