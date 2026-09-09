<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'reference_number',
        'title',
        'category',
        'amount',
        'expense_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Category helpers ─────────────────────────────────────────────────────

    public static function categories(): array
    {
        return [
            'rent'        => '🏠 Rent / Lease',
            'salaries'    => '👥 Salaries & Wages',
            'utilities'   => '💡 Utilities (Electric, Gas, Water)',
            'transport'   => '🚚 Transport & Logistics',
            'marketing'   => '📣 Marketing & Advertising',
            'repairs'     => '🔧 Repairs & Maintenance',
            'purchases'   => '🛒 Office / Store Purchases',
            'insurance'   => '🛡️ Insurance',
            'taxes'       => '🏛️ Taxes & Duties',
            'misc'        => '📦 Miscellaneous',
        ];
    }
}
