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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
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
}
