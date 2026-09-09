<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'credit_balance',
        'credit_limit',
        'credit_limit_enabled',
    ];

    protected $casts = [
        'credit_balance'       => 'decimal:2',
        'credit_limit'         => 'decimal:2',
        'credit_limit_enabled' => 'boolean',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Current outstanding balance = sum of unpaid amounts across all sales.
     *
     * IMPORTANT: This is a PHP accessor — NOT a database column.
     * Never use `balance` in raw SQL WHERE clauses; always filter in PHP.
     */
    public function getBalanceAttribute(): float
    {
        return (float) $this->sales()
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->selectRaw('COALESCE(SUM(total_amount - paid_amount), 0) as outstanding')
            ->value('outstanding') ?? 0;
    }
}