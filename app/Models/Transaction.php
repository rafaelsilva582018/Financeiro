<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'description',
        'total_value',
        'start_date',
        'is_fixed',
        'installments',
        'account_id',
        'credit_card_id',
        'category_id',
    ];

    protected $casts = [
        'is_fixed'     => 'boolean',
        'start_date'   => 'date',
        'total_value'  => 'decimal:2',
        'installments' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    // 🔒 Garante que sempre filtre pelo usuário logado
    public function scopeFromUser(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class)->orderBy('reference_date')->orderBy('id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS (qualidade de vida)
    |--------------------------------------------------------------------------
    */

    public function isIncome(): bool
    {
        return $this->type === 'income';
    }

    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }

    public function formattedValue(): string
    {
        return 'R$ ' . number_format($this->total_value, 2, ',', '.');
    }
}
