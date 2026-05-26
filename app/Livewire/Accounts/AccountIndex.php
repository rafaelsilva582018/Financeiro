<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use Livewire\Component;

class AccountIndex extends Component
{
    protected $listeners = ['account-saved' => '$refresh'];

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