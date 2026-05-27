<?php

use App\Livewire\Transactions\TransactionForm;
use App\Models\Account;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CreateTransactionService;
use App\Services\MonthlySummaryService;
use Carbon\Carbon;
use Livewire\Livewire;

test('bank account expenses are created as paid and appear in monthly summary', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $account = Account::create([
        'user_id' => $user->id,
        'name' => 'Conta corrente',
        'type' => 'checking',
        'initial_balance' => 0,
    ]);

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Mercado',
        'type' => 'expense',
    ]);

    $transaction = app(CreateTransactionService::class)->execute([
        'type' => 'expense',
        'description' => 'Compra do mes',
        'total_value' => 150,
        'start_date' => '2026-05-10',
        'is_fixed' => false,
        'account_id' => $account->id,
        'credit_card_id' => null,
        'installments' => null,
        'category_id' => $category->id,
    ]);

    expect($transaction->entries)->toHaveCount(1)
        ->and($transaction->entries->first()->status)->toBe('paid');

    $summary = app(MonthlySummaryService::class)
        ->getSummary($user->id, Carbon::create(2026, 5, 1));

    expect($summary['expenses'])->toBe(150.0);
});

test('credit card expenses remain pending until payment control', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Cartao',
        'type' => 'expense',
    ]);

    $card = CreditCard::create([
        'user_id' => $user->id,
        'name' => 'Principal',
        'limit' => 1000,
        'closing_day' => 20,
        'due_day' => 10,
    ]);

    $transaction = app(CreateTransactionService::class)->execute([
        'type' => 'expense',
        'description' => 'Compra parcelada',
        'total_value' => 338,
        'start_date' => '2026-05-10',
        'is_fixed' => false,
        'account_id' => null,
        'credit_card_id' => $card->id,
        'installments' => 3,
        'category_id' => $category->id,
    ]);

    expect($transaction->entries)->toHaveCount(3)
        ->and($transaction->entries->pluck('status')->unique()->values()->all())->toBe(['pending'])
        ->and((float) $transaction->entries->sum('value'))->toBe(338.0)
        ->and($transaction->entries->pluck('installment_number')->values()->all())->toBe([1, 2, 3])
        ->and($transaction->entries->pluck('installments_total')->unique()->values()->all())->toBe([3])
        ->and($transaction->entries->first()->due_date->format('Y-m-d'))->toBe('2026-05-10');
});

test('transaction form creates credit card purchase using total value', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Moveis',
        'type' => 'expense',
    ]);

    $card = CreditCard::create([
        'user_id' => $user->id,
        'name' => 'Principal',
        'limit' => 5000,
        'closing_day' => 20,
        'due_day' => 10,
    ]);

    Livewire::test(TransactionForm::class)
        ->set('type', 'expense')
        ->set('description', 'Sofa')
        ->set('total_value', 1000)
        ->set('start_date', '2026-05-10')
        ->set('category_id', $category->id)
        ->set('credit_card_id', $card->id)
        ->set('card_value_mode', 'total')
        ->set('installments', 3)
        ->call('save')
        ->assertHasNoErrors();

    $transaction = Transaction::where('user_id', $user->id)
        ->where('description', 'Sofa')
        ->firstOrFail();

    expect((float) $transaction->total_value)->toBe(1000.0)
        ->and($transaction->entries)->toHaveCount(3)
        ->and((float) $transaction->entries->sum('value'))->toBe(1000.0)
        ->and($transaction->entries->pluck('value')->map(fn ($value) => (float) $value)->all())->toBe([333.34, 333.33, 333.33])
        ->and($transaction->entries->pluck('installment_number')->values()->all())->toBe([1, 2, 3]);
});

test('transaction form creates credit card purchase using installment value', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Eletronicos',
        'type' => 'expense',
    ]);

    $card = CreditCard::create([
        'user_id' => $user->id,
        'name' => 'Principal',
        'limit' => 5000,
        'closing_day' => 20,
        'due_day' => 10,
    ]);

    Livewire::test(TransactionForm::class)
        ->set('type', 'expense')
        ->set('description', 'Notebook')
        ->set('start_date', '2026-05-10')
        ->set('category_id', $category->id)
        ->set('credit_card_id', $card->id)
        ->set('card_value_mode', 'installment')
        ->set('installments', 3)
        ->set('installment_value', 250)
        ->call('save');

    $transaction = Transaction::where('user_id', $user->id)
        ->where('description', 'Notebook')
        ->firstOrFail();

    expect((float) $transaction->total_value)->toBe(750.0)
        ->and($transaction->account_id)->toBeNull()
        ->and($transaction->is_fixed)->toBeFalse()
        ->and($transaction->entries)->toHaveCount(3)
        ->and((float) $transaction->entries->sum('value'))->toBe(750.0)
        ->and((float) $transaction->entries->first()->value)->toBe(250.0)
        ->and($transaction->entries->pluck('installment_number')->values()->all())->toBe([1, 2, 3]);
});

test('transaction form disables fixed flag when saving credit card purchase', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $account = Account::create([
        'user_id' => $user->id,
        'name' => 'Conta corrente',
        'type' => 'checking',
        'initial_balance' => 0,
    ]);

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Mercado',
        'type' => 'expense',
    ]);

    $card = CreditCard::create([
        'user_id' => $user->id,
        'name' => 'Principal',
        'limit' => 1000,
        'closing_day' => 20,
        'due_day' => 10,
    ]);

    Livewire::test(TransactionForm::class)
        ->set('type', 'expense')
        ->set('description', 'Compra no cartao')
        ->set('total_value', 120)
        ->set('start_date', '2026-05-10')
        ->set('category_id', $category->id)
        ->set('account_id', $account->id)
        ->set('is_fixed', true)
        ->set('credit_card_id', $card->id)
        ->set('installments', 1)
        ->call('save')
        ->assertHasNoErrors();

    $transaction = Transaction::where('user_id', $user->id)
        ->where('description', 'Compra no cartao')
        ->firstOrFail();

    expect($transaction->is_fixed)->toBeFalse()
        ->and($transaction->account_id)->toBeNull()
        ->and($transaction->credit_card_id)->toBe($card->id)
        ->and($transaction->entries)->toHaveCount(1);
});
