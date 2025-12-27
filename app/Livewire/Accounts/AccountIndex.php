<?php

namespace App\Livewire\Accounts;

use Livewire\Component;
use App\Models\Account;

class AccountIndex extends Component
{
    public function delete(int $id): void
    {
        Account::where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();
    }

    public function render()
    {
        return view('livewire.accounts.account-index', [
            'accounts' => Account::where('user_id', auth()->id())
                ->orderBy('name')
                ->get(),
        ]);
    }
}
