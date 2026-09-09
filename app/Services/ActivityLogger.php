<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log a business event.
     *
     * @param string      $action      created|updated|deleted|login|logout|paid|returned
     * @param string      $description Human-readable summary
     * @param string|null $model       Class name (e.g. 'Sale')
     * @param int|null    $modelId     Primary key of the model
     * @param array|null  $oldValues   Previous values (for updates)
     * @param array|null  $newValues   New values
     */
    public static function log(
        string $action,
        string $description,
        ?string $model = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => $action,
                'model'       => $model,
                'model_id'    => $modelId,
                'description' => $description,
                'old_values'  => $oldValues,
                'new_values'  => $newValues,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the main request
            logger()->error('ActivityLogger failed: ' . $e->getMessage());
        }
    }

    // ── Convenience wrappers ─────────────────────────────────────────────────

    public static function created(string $model, int $id, string $description): void
    {
        static::log('created', $description, $model, $id);
    }

    public static function updated(string $model, int $id, string $description, array $old = [], array $new = []): void
    {
        static::log('updated', $description, $model, $id, $old ?: null, $new ?: null);
    }

    public static function deleted(string $model, int $id, string $description): void
    {
        static::log('deleted', $description, $model, $id);
    }

    public static function paid(string $model, int $id, string $description): void
    {
        static::log('paid', $description, $model, $id);
    }

    public static function login(string $description): void
    {
        static::log('login', $description);
    }

    public static function logout(string $description): void
    {
        static::log('logout', $description);
    }
}
