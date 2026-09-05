<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\customer;
use App\Models\User;
use App\Models\SaleItem;
class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'sale_date',
        'invoice_number',
        'total_amount',
        'discount',
        'tax',
        'paid_amount',
        'payment_status',
        'payment_method',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
{
    return $this->hasMany(Payment::class);
}
}