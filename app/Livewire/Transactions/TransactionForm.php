<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Category;
use App\Models\Account;
use App\Models\CreditCard;
use App\Services\CreateTransactionService;

class TransactionForm extends Component
{
    public string $type = '';
    public string $description = '';
    public float $total_value = 0;
    public string $start_date = '';
    public bool $is_fixed = false;
    public ?int $installments = null;

    public ?int $account_id = null;
    public ?int $credit_card_id = null;
    public ?int $category_id = null;

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

    public function save(CreateTransactionService $service): void
    {
        $data = $this->validate();

        $service->execute($data);

        // ✅ Mensagem traduzida (única mudança)
        session()->flash('success', __('transaction.created'));

        $this->reset([
            'description',
            'total_value',
            'start_date',
            'is_fixed',
            'installments',
            'account_id',
            'credit_card_id',
            'category_id',
        ]);
    }

    public function render()
    {
        return view('livewire.transactions.transaction-form', [
            'categories' => Category::where('user_id', auth()->id())
                ->where('type', $this->type)
                ->get(),

            'accounts' => Account::where('user_id', auth()->id())->get(),
            'cards' => CreditCard::where('user_id', auth()->id())->get(),
        ]);
    }
}
