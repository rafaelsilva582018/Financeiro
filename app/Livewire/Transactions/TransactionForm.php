<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\Transaction;
use App\Services\CreateTransactionService;
use Carbon\Carbon;
use Livewire\Component;

class TransactionForm extends Component
{
    public ?Transaction $transaction = null;

    public bool $modal = false;

    public string $type = 'expense';

    public string $description = '';

    public float $total_value = 0;

    public string $card_value_mode = 'total';

    public float $installment_value = 0;

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
            'card_value_mode' => 'required|in:total,installment',
            'installment_value' => 'exclude_unless:card_value_mode,installment|required|numeric|min:0.01',
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
            'installment_value.required_if' => 'Informe o valor da parcela.',
            'installment_value.min' => 'O valor da parcela deve ser maior que zero.',
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
            'installment_value' => 'valor da parcela',
            'start_date' => 'data',
            'category_id' => 'categoria',
            'account_id' => 'conta bancária',
            'credit_card_id' => 'cartão de crédito',
            'installments' => 'parcelas',
        ];
    }

    public function updatedCreditCardId($value): void
    {
        if ($value) {
            $this->account_id = null;
            $this->is_fixed = false;
            $this->installments = $this->installments ?: 1;

            return;
        }

        if (! $value) {
            $this->card_value_mode = 'total';
            $this->installment_value = 0;
            $this->installments = null;
        }
    }

    public function updatedType(): void
    {
        $this->category_id = null;

        if ($this->type === 'income') {
            $this->credit_card_id = null;
            $this->card_value_mode = 'total';
            $this->installment_value = 0;
            $this->installments = null;
        }
    }

    public function selectType(string $type): void
    {
        if (! in_array($type, ['income', 'expense'], true)) {
            return;
        }

        $this->type = $type;
        $this->updatedType();
    }

    public function updatedCardValueMode(string $value): void
    {
        if ($value === 'total') {
            $this->installment_value = 0;
        }
    }

    public function selectCardValueMode(string $mode): void
    {
        if (! in_array($mode, ['total', 'installment'], true)) {
            return;
        }

        $this->card_value_mode = $mode;
        $this->updatedCardValueMode($mode);
    }

    public function save(CreateTransactionService $service)
    {
        if ($this->credit_card_id) {
            $this->account_id = null;
            $this->is_fixed = false;
            $this->installments = max((int) ($this->installments ?: 1), 1);
        }

        if ($this->credit_card_id && $this->card_value_mode === 'installment') {
            $this->total_value = round($this->installment_value * max((int) $this->installments, 1), 2);
        }

        $data = $this->validate();
        $this->validateTransactionDate($data['start_date']);
        unset($data['card_value_mode'], $data['installment_value']);

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

    private function validateTransactionDate(string $date): void
    {
        $year = Carbon::parse($date)->year;
        $currentYear = now()->year;

        if ($year < ($currentYear - 2)) {
            $this->addError('start_date', 'Confira o ano informado. A data parece antiga demais.');
            throw \Illuminate\Validation\ValidationException::withMessages([
                'start_date' => 'Confira o ano informado. A data parece antiga demais.',
            ]);
        }
    }

    private function resetForm(): void
    {
        $type = $this->type;
        $modal = $this->modal;

        $this->reset([
            'transaction',
            'description',
            'total_value',
            'card_value_mode',
            'installment_value',
            'start_date',
            'is_fixed',
            'installments',
            'account_id',
            'credit_card_id',
            'category_id',
        ]);

        $this->type = $type;
        $this->card_value_mode = 'total';
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
