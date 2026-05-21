<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use Livewire\Component;

class AccountForm extends Component
{
    public ?Account $account = null;

    public bool $modal = false;

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

    protected function messages(): array
    {
        return [
            'name.required' => 'Informe o nome da conta.',
            'type.required' => 'Selecione o tipo da conta.',
            'initial_balance.required' => 'Informe o saldo inicial.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name' => 'nome',
            'type' => 'tipo',
            'initial_balance' => 'saldo inicial',
        ];
    }

    public function mount(?Account $account = null, bool $modal = false): void
    {
        $this->modal = $modal;

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
            ['id' => $this->account?->id],
            [
                'user_id' => auth()->id(),
                'name' => $this->name,
                'type' => $this->type,
                'initial_balance' => $this->initial_balance,
            ]
        );

        if ($this->modal) {
            $this->reset(['account', 'name', 'initial_balance']);
            $this->type = 'wallet';
            $this->modal = true;
            $this->dispatch('account-saved');
            $this->dispatch('close-resource-modal');

            return null;
        }

        return redirect()->route('accounts.index');
    }

    public function render()
    {
        return view('livewire.accounts.account-form');
    }
}