<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankImportRule extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'transaction_id',
        'type',
        'keyword',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
