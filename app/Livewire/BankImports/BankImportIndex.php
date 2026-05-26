<?php

namespace App\Livewire\BankImports;

use App\Models\Account;
use App\Models\BankImport;
use App\Models\BankImportRule;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\Entry;
use App\Models\Transaction;
use App\Services\CreateTransactionService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class BankImportIndex extends Component
{
    public string $type = 'expense';

    public string $description = '';

    public float $amount = 0;

    public string $occurred_at = '';

    public ?int $account_id = null;

    public array $categorySelection = [];

    public array $entrySelection = [];

    public array $accountSelection = [];

    public array $saveRuleSelection = [];

    public array $invoiceCardSelection = [];

    public array $invoiceMonthSelection = [];

    public function mount(): void
    {
        $this->occurred_at = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'type' => 'required|in:income,expense',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'occurred_at' => 'required|date',
            'account_id' => 'nullable|exists:accounts,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'description.required' => 'Informe a descrição que veio do banco.',
            'amount.required' => 'Informe o valor.',
            'amount.min' => 'O valor precisa ser maior que zero.',
            'occurred_at.required' => 'Informe a data.',
        ];
    }

    public function createImport(): void
    {
        $data = $this->validate();

        $import = BankImport::create([
            'user_id' => auth()->id(),
            'account_id' => $data['account_id'] ?? null,
            'source' => 'manual',
            'type' => $data['type'],
            'description' => $data['description'],
            'amount' => $data['amount'],
            'occurred_at' => $data['occurred_at'],
            'status' => 'pending',
        ]);

        $this->applyRules($import);

        $this->reset(['description', 'amount', 'account_id']);
        $this->type = 'expense';
        $this->occurred_at = now()->format('Y-m-d');

        session()->flash('success', 'Movimentação enviada para pendências.');
    }

    public function confirmCategory(int $importId): void
    {
        $import = $this->findPendingImport($importId);
        $categoryId = (int) ($this->categorySelection[$importId] ?? $import->suggested_category_id ?? 0);

        if ($categoryId <= 0) {
            $this->addError("categorySelection.$importId", 'Escolha uma categoria.');
            return;
        }

        $category = Category::where('user_id', auth()->id())
            ->where('type', $import->type)
            ->findOrFail($categoryId);

        $import->update([
            'category_id' => $category->id,
        ]);

        if ($this->shouldSaveRule($importId)) {
            $this->saveRule($import, $category->id, null);
        }

        session()->flash('success', 'Categoria aplicada.');
    }

    public function linkToEntry(int $importId): void
    {
        $import = $this->findPendingImport($importId);
        $entryId = (int) ($this->entrySelection[$importId] ?? 0);

        if ($entryId <= 0) {
            $this->addError("entrySelection.$importId", 'Escolha um lançamento para vincular.');
            return;
        }

        $entry = Entry::with('transaction')
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($entryId);

        if ($entry->transaction?->type !== $import->type) {
            $this->addError("entrySelection.$importId", 'Escolha um lançamento do mesmo tipo.');
            return;
        }

        $entry->update(['status' => 'paid']);

        $import->update([
            'status' => 'linked',
            'entry_id' => $entry->id,
            'transaction_id' => $entry->transaction_id,
            'category_id' => $entry->transaction?->category_id,
            'account_id' => $import->account_id ?? $entry->account_id,
        ]);

        if ($this->shouldSaveRule($importId)) {
            $this->saveRule($import, $entry->transaction?->category_id, $entry->transaction_id);
        }

        session()->flash('success', 'Movimentação vinculada e lançamento marcado como pago.');
    }

    public function createTransactionFromImport(int $importId, CreateTransactionService $service): void
    {
        $import = $this->findPendingImport($importId);
        $categoryId = (int) ($this->categorySelection[$importId] ?? $import->category_id ?? $import->suggested_category_id ?? 0);
        $accountId = (int) ($this->accountSelection[$importId] ?? $import->account_id ?? 0);

        if ($categoryId <= 0) {
            $this->addError("categorySelection.$importId", 'Escolha uma categoria para criar a transação.');
            return;
        }

        if ($accountId <= 0) {
            $this->addError("accountSelection.$importId", 'Escolha a conta para criar a transação.');
            return;
        }

        Category::where('user_id', auth()->id())
            ->where('type', $import->type)
            ->findOrFail($categoryId);

        Account::where('user_id', auth()->id())->findOrFail($accountId);

        $transaction = $service->execute([
            'type' => $import->type,
            'description' => $import->description,
            'total_value' => $import->amount,
            'start_date' => $import->occurred_at->format('Y-m-d'),
            'is_fixed' => false,
            'installments' => null,
            'account_id' => $accountId,
            'credit_card_id' => null,
            'category_id' => $categoryId,
        ]);

        $entry = $transaction->entries()->first();

        $import->update([
            'status' => 'created',
            'account_id' => $accountId,
            'category_id' => $categoryId,
            'transaction_id' => $transaction->id,
            'entry_id' => $entry?->id,
        ]);

        if ($this->shouldSaveRule($importId)) {
            $this->saveRule($import, $categoryId, null);
        }

        session()->flash('success', 'Transação criada a partir da movimentação.');
    }

    public function closeCardInvoice(int $importId): void
    {
        $import = $this->findPendingImport($importId);

        if ($import->type !== 'expense') {
            $this->addError("invoiceCardSelection.$importId", 'Pagamento de fatura precisa ser uma saída.');
            return;
        }

        $cardId = (int) ($this->invoiceCardSelection[$importId] ?? 0);
        $invoiceMonth = (string) ($this->invoiceMonthSelection[$importId] ?? $import->occurred_at->format('Y-m'));

        if ($cardId <= 0) {
            $this->addError("invoiceCardSelection.$importId", 'Escolha o cartão da fatura.');
            return;
        }

        if (! preg_match('/^\d{4}-\d{2}$/', $invoiceMonth)) {
            $this->addError("invoiceMonthSelection.$importId", 'Escolha o mês da fatura.');
            return;
        }

        $card = CreditCard::where('user_id', auth()->id())->findOrFail($cardId);
        $reference = Carbon::createFromFormat('Y-m-d', "{$invoiceMonth}-01");
        $start = $reference->copy()->startOfMonth();
        $end = $reference->copy()->endOfMonth();

        $entries = Entry::where('user_id', auth()->id())
            ->where('credit_card_id', $card->id)
            ->where('status', 'pending')
            ->whereBetween('reference_date', [$start, $end])
            ->get();

        if ($entries->isEmpty()) {
            $this->addError("invoiceMonthSelection.$importId", 'Não encontrei compras pendentes nessa fatura.');
            return;
        }

        $paidTotal = (float) $entries->sum('value');

        Entry::whereIn('id', $entries->pluck('id'))->update([
            'status' => 'paid',
        ]);

        $import->update([
            'status' => 'linked',
            'raw_payload' => array_merge($import->raw_payload ?? [], [
                'card_invoice_payment' => [
                    'credit_card_id' => $card->id,
                    'credit_card_name' => $card->name,
                    'invoice_month' => $invoiceMonth,
                    'paid_entries' => $entries->pluck('id')->values()->all(),
                    'invoice_total' => $paidTotal,
                    'bank_payment_amount' => (float) $import->amount,
                ],
            ]),
        ]);

        session()->flash('success', "Fatura de {$card->name} fechada. {$entries->count()} lançamentos foram marcados como pagos.");
    }

    public function applySuggestion(int $importId): void
    {
        $import = $this->findPendingImport($importId);

        if ($import->suggested_category_id) {
            $this->categorySelection[$importId] = $import->suggested_category_id;
            $this->confirmCategory($importId);
        }

        if (! $import->suggested_transaction_id) {
            return;
        }

        $entry = Entry::where('user_id', auth()->id())
            ->where('transaction_id', $import->suggested_transaction_id)
            ->where('status', 'pending')
            ->where('value', $import->amount)
            ->orderBy('reference_date')
            ->first();

        if (! $entry) {
            $this->addError("entrySelection.$importId", 'Não encontrei lançamento pendente compatível para aplicar essa regra.');
            return;
        }

        $this->entrySelection[$importId] = $entry->id;
        $this->linkToEntry($importId);
    }

    public function ignoreImport(int $importId): void
    {
        $this->findPendingImport($importId)->update(['status' => 'ignored']);

        session()->flash('success', 'Movimentação ignorada.');
    }

    private function findPendingImport(int $importId): BankImport
    {
        return BankImport::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($importId);
    }

    private function applyRules(BankImport $import): void
    {
        $description = $this->normalize($import->description);

        $rule = BankImportRule::where('user_id', auth()->id())
            ->where('active', true)
            ->where(function ($query) use ($import) {
                $query->whereNull('type')->orWhere('type', $import->type);
            })
            ->get()
            ->first(fn (BankImportRule $rule) => str_contains($description, $this->normalize($rule->keyword)));

        if (! $rule) {
            return;
        }

        $import->update([
            'suggested_category_id' => $rule->category_id,
            'suggested_transaction_id' => $rule->transaction_id,
        ]);
    }

    private function shouldSaveRule(int $importId): bool
    {
        return (bool) ($this->saveRuleSelection[$importId] ?? false);
    }

    private function saveRule(BankImport $import, ?int $categoryId, ?int $transactionId): void
    {
        BankImportRule::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'keyword' => $this->makeKeyword($import->description),
                'type' => $import->type,
            ],
            [
                'category_id' => $categoryId,
                'transaction_id' => $transactionId,
                'active' => true,
            ],
        );
    }

    private function makeKeyword(string $description): string
    {
        return Str::limit($this->normalize($description), 60, '');
    }

    private function normalize(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', Str::of($value)->ascii()->lower()->replaceMatches('/[^a-z0-9 ]/', ' ')->toString()));
    }

    public function render()
    {
        $imports = BankImport::with(['account', 'category', 'suggestedCategory', 'suggestedTransaction'])
            ->where('user_id', auth()->id())
            ->orderByRaw("status = 'pending' desc")
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->get();

        $openEntries = Entry::with(['transaction.category', 'account', 'creditCard'])
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->whereHas('transaction')
            ->orderBy('reference_date')
            ->get();

        return view('livewire.bank-imports.bank-import-index', [
            'imports' => $imports,
            'openEntries' => $openEntries,
            'accounts' => Account::where('user_id', auth()->id())->orderBy('name')->get(),
            'cards' => CreditCard::where('user_id', auth()->id())->orderBy('name')->get(),
            'categories' => Category::where('user_id', auth()->id())->orderBy('type')->orderBy('name')->get(),
            'rules' => BankImportRule::with(['category', 'transaction'])
                ->where('user_id', auth()->id())
                ->where('active', true)
                ->orderBy('keyword')
                ->get(),
        ]);
    }
}
