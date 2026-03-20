<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    public static function bootLoggable()
    {
        static::created(function ($model) {
            self::logActivity($model, 'CREATE');
        });

        static::updated(function ($model) {
            self::logActivity($model, 'UPDATE');
        });

        static::deleted(function ($model) {
            self::logActivity($model, 'DELETE');
        });
    }

    protected static function logActivity($model, $action)
    {
        // ActivityLog::create([
        //     'user_id' => Auth::id() ?? 1, // Default ke ID 1 jika sistem/console
        //     'module' => class_basename($model),
        //     'action' => $action,
        //     'target_id' => $model->id,
        //     'old_data' => $action === 'CREATE' ? null : $model->getOriginal(),
        //     'new_data' => $action === 'DELETE' ? null : $model->getAttributes(),
        //     'ip_address' => request()->ip(),
        // ]);
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => class_basename($model),
            'action' => $action,
            'target_id' => $model->id,
            // Saat DELETE, simpan semua data yang ada sebelum dihapus ke old_data
            'old_data' => ($action === 'DELETE' || $action === 'UPDATE') ? $model->getRawOriginal() : null,
            'new_data' => ($action === 'CREATE' || $action === 'UPDATE') ? $model->getAttributes() : null,
            'ip_address' => request()->ip(),
        ]);
    }
}
