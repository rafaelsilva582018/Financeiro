<?php

use App\Livewire\BankImports\BankImportIndex;
use App\Models\Account;
use App\Models\BankImport;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\Entry;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Livewire;

test('authenticated users can visit bank imports review page', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/bank-imports')->assertStatus(200);
});

test('bank import can be linked to an existing pending entry', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $account = Account::create([
        'user_id' => $user->id,
        'name' => 'Conta teste',
        'type' => 'checking',
        'initial_balance' => 0,
    ]);

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Internet',
        'type' => 'expense',
    ]);

    $transaction = Transaction::create([
        'user_id' => $user->id,
        'type' => 'expense',
        'description' => 'Internet',
        'total_value' => 99.90,
        'start_date' => '2026-05-10',
        'is_fixed' => false,
        'installments' => null,
        'account_id' => $account->id,
        'category_id' => $category->id,
    ]);

    $entry = Entry::create([
        'user_id' => $user->id,
        'transaction_id' => $transaction->id,
        'reference_date' => '2026-05-01',
        'value' => 99.90,
        'status' => 'pending',
        'account_id' => $account->id,
    ]);

    $import = BankImport::create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'source' => 'manual',
        'type' => 'expense',
        'description' => 'PIX INTERNET FIBRA',
        'amount' => 99.90,
        'occurred_at' => '2026-05-10',
        'status' => 'pending',
    ]);

    Livewire::test(BankImportIndex::class)
        ->set("entrySelection.$import->id", $entry->id)
        ->call('linkToEntry', $import->id);

    expect($entry->refresh()->status)->toBe('paid');
    expect($import->refresh()->status)->toBe('linked');
expect($import->entry_id)->toBe($entry->id);
});

test('bank import can close a credit card invoice', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $account = Account::create([
        'user_id' => $user->id,
        'name' => 'Conta banco',
        'type' => 'bank',
        'initial_balance' => 0,
    ]);

    $card = CreditCard::create([
        'user_id' => $user->id,
        'name' => 'C6 Carbon',
        'limit' => 5000,
        'closing_day' => 20,
        'due_day' => 10,
    ]);

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Mercado',
        'type' => 'expense',
    ]);

    $firstTransaction = Transaction::create([
        'user_id' => $user->id,
        'type' => 'expense',
        'description' => 'Mercado',
        'total_value' => 300,
        'start_date' => '2026-05-05',
        'is_fixed' => false,
        'installments' => 1,
        'credit_card_id' => $card->id,
        'category_id' => $category->id,
    ]);

    $secondTransaction = Transaction::create([
        'user_id' => $user->id,
        'type' => 'expense',
        'description' => 'Farmácia',
        'total_value' => 80,
        'start_date' => '2026-05-08',
        'is_fixed' => false,
        'installments' => 1,
        'credit_card_id' => $card->id,
        'category_id' => $category->id,
    ]);

    $firstEntry = Entry::create([
        'user_id' => $user->id,
        'transaction_id' => $firstTransaction->id,
        'reference_date' => '2026-05-01',
        'value' => 300,
        'status' => 'pending',
        'credit_card_id' => $card->id,
    ]);

    $secondEntry = Entry::create([
        'user_id' => $user->id,
        'transaction_id' => $secondTransaction->id,
        'reference_date' => '2026-05-01',
        'value' => 80,
        'status' => 'pending',
        'credit_card_id' => $card->id,
    ]);

    $nextMonthEntry = Entry::create([
        'user_id' => $user->id,
        'transaction_id' => $secondTransaction->id,
        'reference_date' => '2026-06-01',
        'value' => 80,
        'status' => 'pending',
        'credit_card_id' => $card->id,
    ]);

    $import = BankImport::create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'source' => 'manual',
        'type' => 'expense',
        'description' => 'PAGAMENTO FATURA CARTAO',
        'amount' => 380,
        'occurred_at' => '2026-05-10',
        'status' => 'pending',
    ]);

    Livewire::test(BankImportIndex::class)
        ->set("invoiceCardSelection.$import->id", $card->id)
        ->set("invoiceMonthSelection.$import->id", '2026-05')
        ->call('closeCardInvoice', $import->id);

    expect($firstEntry->refresh()->status)->toBe('paid');
    expect($secondEntry->refresh()->status)->toBe('paid');
    expect($nextMonthEntry->refresh()->status)->toBe('pending');
    expect($import->refresh()->status)->toBe('linked');
    expect((float) $import->raw_payload['card_invoice_payment']['invoice_total'])->toBe(380.0);
});
