<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'is_fixed' => 'boolean',
        'start_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
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
