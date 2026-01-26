<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Category;
use App\Models\Account;
use App\Models\CreditCard;
use App\Models\Transaction;
use App\Services\CreateTransactionService;

class TransactionForm extends Component
{
    public ?Transaction $transaction = null;

    public string $type = '';
    public string $description = '';
    public float $total_value = 0;
    public string $start_date = '';
    public bool $is_fixed = false;
    public ?int $installments = null;

    public ?int $account_id = null;
    public ?int $credit_card_id = null;
    public ?int $category_id = null;

    /*
    |--------------------------------------------------------------------------
    | MOUNT (CARREGA DADOS SE FOR EDIÇÃO)
    |--------------------------------------------------------------------------
    */
    public function mount(Transaction $transaction = null)
    {
        if ($transaction && $transaction->user_id === auth()->id()) {

            $this->transaction = $transaction;

            $this->type = $transaction->type;
            $this->description = $transaction->description;
            $this->total_value = $transaction->total_value;
            $this->start_date = $transaction->start_date->format('Y-m-d');
            $this->is_fixed = $transaction->is_fixed;
            $this->installments = $transaction->installments;
            $this->account_id = $transaction->account_id;
            $this->credit_card_id = $transaction->credit_card_id;
            $this->category_id = $transaction->category_id;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REGRAS
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | SAVE (CRIA OU ATUALIZA)
    |--------------------------------------------------------------------------
    */
    public function save(CreateTransactionService $service)
    {
        $data = $this->validate();

        if ($this->transaction) {
            // ✏️ UPDATE
            $this->transaction->update($data);

            session()->flash('success', 'Transação atualizada com sucesso.');
        } else {
            // ➕ CREATE
            $service->execute($data);

            session()->flash('success', __('transaction.created'));
        }

        return redirect()->route('transactions.index');
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */
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
