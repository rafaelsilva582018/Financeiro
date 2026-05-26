<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\User;
use App\Services\CreateTransactionService;
use App\Services\MonthlySummaryService;
use Carbon\Carbon;

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
        ->and((float) $transaction->entries->sum('value'))->toBe(338.0);
});
