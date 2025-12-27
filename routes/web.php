<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Livewire - Settings
|--------------------------------------------------------------------------
*/
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;

/*
|--------------------------------------------------------------------------
| Livewire - Gestão
|--------------------------------------------------------------------------
*/
use App\Livewire\Dashboard;

use App\Livewire\Categories\CategoryIndex;
use App\Livewire\Categories\CategoryForm;

use App\Livewire\Accounts\AccountIndex;
use App\Livewire\Accounts\AccountForm;

use App\Livewire\CreditCards\CreditCardIndex;
use App\Livewire\CreditCards\CreditCardForm;

/*
|--------------------------------------------------------------------------
| Livewire - Operações
|--------------------------------------------------------------------------
*/
use App\Livewire\Transactions\TransactionIndex;
use App\Livewire\Transactions\TransactionForm;

use App\Livewire\Entries\EntryIndex;

/*
|--------------------------------------------------------------------------
| Rotas públicas
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rotas autenticadas
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)
        ->name('profile.edit');

    Route::get('settings/password', Password::class)
        ->name('user-password.edit');

    Route::get('settings/appearance', Appearance::class)
        ->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(
                    Features::twoFactorAuthentication(),
                    'confirmPassword'
                ),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    /*
    |--------------------------------------------------------------------------
    | Gestão
    |--------------------------------------------------------------------------
    */
    Route::get('/categories', CategoryIndex::class)
        ->name('categories.index');

    Route::get('/categories/create', CategoryForm::class)
        ->name('categories.create');

    Route::get('/categories/{category}/edit', CategoryForm::class)
        ->name('categories.edit');

    Route::get('/accounts', AccountIndex::class)
        ->name('accounts.index');

    Route::get('/accounts/create', AccountForm::class)
        ->name('accounts.create');

    Route::get('/accounts/{account}/edit', AccountForm::class)
        ->name('accounts.edit');

    Route::get('/credit-cards', CreditCardIndex::class)
        ->name('credit-cards.index');

    Route::get('/credit-cards/create', CreditCardForm::class)
        ->name('credit-cards.create');

    Route::get('/credit-cards/{creditCard}/edit', CreditCardForm::class)
        ->name('credit-cards.edit');

    /*
    |--------------------------------------------------------------------------
    | Operações
    |--------------------------------------------------------------------------
    */
    Route::get('/transactions', TransactionIndex::class)
        ->name('transactions.index');

    Route::get('/transactions/create', TransactionForm::class)
        ->name('transactions.create');

    Route::get('/entries', EntryIndex::class)
        ->name('entries.index');
});
