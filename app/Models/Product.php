<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Models\StockMovement;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'purchase_price',
        'selling_price',
        'stock_quantity',
        'unit',
        'reorder_level',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(
            Category::class,
            'category_id',
            'id'
        );
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements()
{
    return $this->hasMany(StockMovement::class);
}
}