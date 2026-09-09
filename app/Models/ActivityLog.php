<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Action icons ─────────────────────────────────────────────────────────

    public function getIconAttribute(): string
    {
        return match($this->action) {
            'created'  => '✅',
            'updated'  => '✏️',
            'deleted'  => '🗑️',
            'login'    => '🔑',
            'logout'   => '🚪',
            'paid'     => '💰',
            'returned' => '↩️',
            default    => '📋',
        };
    }

    // ── Color ─────────────────────────────────────────────────────────────────

    public function getBadgeClassAttribute(): string
    {
        return match($this->action) {
            'created'  => 'badge-success',
            'updated'  => 'badge-warning',
            'deleted'  => 'badge-danger',
            'login'    => 'badge-cyan',
            'logout'   => 'badge-gray',
            'paid'     => 'badge-success',
            'returned' => 'badge-warning',
            default    => 'badge-gray',
        };
    }
}
