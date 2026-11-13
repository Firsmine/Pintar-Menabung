<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'money_lover_transactions';

    protected $fillable = [
        'category_id',
        'wallet_id',
        'amount',
        'note',
        'date'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
}
