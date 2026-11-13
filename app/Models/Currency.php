<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'money_lover_currencies';

    protected $fillable = [
        'name',
        'symbol',
        'code',
    ];

    public function wallet()
    {
        return $this->hasMany(Wallet::class, 'currency_id');
    }
}
