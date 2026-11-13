<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'money_lover_categories';

    protected $fillable = [
        'name',
        'icon',
        'type',
    ];

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
}
