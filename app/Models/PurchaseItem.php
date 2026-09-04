<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase;
use App\Models\Product;

class PurchaseItem extends Model
{
    protected $fillable = [
    'purchase_id',
    'product_id',
    'quantity',
    'unit_price',
    'subtotal',
];
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
