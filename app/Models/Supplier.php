<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase;
class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
    ];
     
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }
}
