<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'reference_date',
        'value',
        'status',
        'account_id',
        'credit_card_id',
    ];

    protected $casts = [
        'reference_date' => 'date',
        'value' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔒 Protegido contra transações deletadas
    public function transaction()
    {
        return $this->belongsTo(Transaction::class)->withDefault([
            'description' => 'Transação removida',
            'type' => null,
        ]);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function formattedValue(): string
    {
        return 'R$ ' . number_format($this->value, 2, ',', '.');
    }
}
