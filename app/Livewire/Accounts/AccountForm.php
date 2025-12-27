<?php

namespace App\Livewire\Accounts;

use Livewire\Component;
use App\Models\Account;

class AccountForm extends Component
{
    public ?Account $account = null;

    public string $name = '';
    public string $type = 'wallet';
    public float $initial_balance = 0;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:wallet,bank',
            'initial_balance' => 'required|numeric',
        ];
    }

    public function mount(Account $account = null): void
    {
        if ($account && $account->exists) {
            $this->account = $account;
            $this->name = $account->name;
            $this->type = $account->type;
            $this->initial_balance = (float) $account->initial_balance;
        }
    }

    public function save()
    {
        $this->validate();

        Account::updateOrCreate(
            [
                'id' => $this->account?->id,
            ],
            [
                'user_id' => auth()->id(),
                'name' => $this->name,
                'type' => $this->type,
                'initial_balance' => $this->initial_balance,
            ]
        );

        return redirect()->route('accounts.index');
    }

    public function render()
    {
        return view('livewire.accounts.account-form');
    }
}
