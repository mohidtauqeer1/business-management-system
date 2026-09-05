<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
    'user_id',
    'supplier_id',
    'customer_id',
    'purchase_id',
    'sale_id',
    'type',
    'amount',
    'payment_method',
    'reference_number',
    'notes',
];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function purchase()
{
    return $this->belongsTo(Purchase::class);
}

public function sale()
{
    return $this->belongsTo(Sale::class);
}
}