<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'credit_balance',
    ];

    protected $casts = [
        'credit_balance' => 'decimal:2',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}