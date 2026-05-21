<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\Transaction;
use App\Services\CreateTransactionService;
use Livewire\Component;

class TransactionForm extends Component
{
    public ?Transaction $transaction = null;

    public bool $modal = false;

    public string $type = '';

    public string $description = '';

    public float $total_value = 0;

    public string $start_date = '';

    public bool $is_fixed = false;

    public ?int $installments = null;

    public ?int $account_id = null;

    public ?int $credit_card_id = null;

    public ?int $category_id = null;

    public function mount(?Transaction $transaction = null, ?string $type = null, bool $modal = false): void
    {
        $this->modal = $modal;

        if ($type) {
            $this->type = $type;
        }

        if ($transaction && $transaction->user_id === auth()->id()) {
            $this->transaction = $transaction;
            $this->type = $transaction->type;
            $this->description = $transaction->description;
            $this->total_value = (float) $transaction->total_value;
            $this->start_date = $transaction->start_date->format('Y-m-d');
            $this->is_fixed = $transaction->is_fixed;
            $this->installments = $transaction->installments;
            $this->account_id = $transaction->account_id;
            $this->credit_card_id = $transaction->credit_card_id;
            $this->category_id = $transaction->category_id;
        }
    }

    protected function rules(): array
    {
        return [
            'type' => 'required|in:income,expense',
            'description' => 'required|string|max:255',
            'total_value' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'nullable|required_without:credit_card_id|exists:accounts,id',
            'credit_card_id' => 'nullable|exists:credit_cards,id',
            'installments' => 'nullable|required_with:credit_card_id|integer|min:1',
            'is_fixed' => 'boolean',
        ];
    }


    protected function messages(): array
    {
        return [
            'type.required' => 'Selecione se é receita ou despesa.',
            'description.required' => 'Informe uma descrição.',
            'total_value.required' => 'Informe o valor.',
            'total_value.min' => 'O valor deve ser maior que zero.',
            'start_date.required' => 'Informe a data.',
            'category_id.required' => 'Selecione uma categoria.',
            'account_id.required_without' => 'Selecione uma conta bancária.',
            'installments.required_with' => 'Informe a quantidade de parcelas.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'type' => 'tipo',
            'description' => 'descrição',
            'total_value' => 'valor',
            'start_date' => 'data',
            'category_id' => 'categoria',
            'account_id' => 'conta bancária',
            'credit_card_id' => 'cartão de crédito',
            'installments' => 'parcelas',
        ];
    }
    public function save(CreateTransactionService $service)
    {
        $data = $this->validate();

        if ($this->transaction) {
            $service->update($this->transaction, $data);
            session()->flash('success', 'Transação atualizada com sucesso.');
        } else {
            $service->execute($data);
            session()->flash('success', 'Transação criada com sucesso.');
        }

        if ($this->modal) {
            $this->resetForm();
            $this->dispatch('transaction-created');
            $this->dispatch('close-dashboard-modal');
            $this->dispatch('close-resource-modal');

            return null;
        }

        return redirect()->route('transactions.index');
    }

    private function resetForm(): void
    {
        $type = $this->type;
        $modal = $this->modal;

        $this->reset([
            'transaction',
            'description',
            'total_value',
            'start_date',
            'is_fixed',
            'installments',
            'account_id',
            'credit_card_id',
            'category_id',
        ]);

        $this->type = $type;
        $this->modal = $modal;
    }

    public function render()
    {
        return view('livewire.transactions.transaction-form', [
            'categories' => Category::where('user_id', auth()->id())
                ->where('type', $this->type ?: 'expense')
                ->orderBy('name')
                ->get(),
            'accounts' => Account::where('user_id', auth()->id())->orderBy('name')->get(),
            'cards' => CreditCard::where('user_id', auth()->id())->orderBy('name')->get(),
        ]);
    }
}