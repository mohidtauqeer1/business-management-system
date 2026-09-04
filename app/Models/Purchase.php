<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;
use App\Models\User;
use App\Models\PurchaseItem;
use App\Models\Product;
class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'user_id',
        'purchase_date',
        'invoice_number',
        'total_amount',
        'paid_amount',
        'payment_status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_items', 'purchase_id', 'product_id')
                    ->withPivot('quantity', 'unit_price', 'subtotal')
                    ->withTimestamps();
    }
}