<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankImport extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'entry_id',
        'transaction_id',
        'source',
        'external_id',
        'type',
        'description',
        'amount',
        'occurred_at',
        'status',
        'suggested_category_id',
        'suggested_transaction_id',
        'raw_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'occurred_at' => 'date',
        'raw_payload' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function suggestedCategory()
    {
        return $this->belongsTo(Category::class, 'suggested_category_id');
    }

    public function suggestedTransaction()
    {
        return $this->belongsTo(Transaction::class, 'suggested_transaction_id');
    }
}
