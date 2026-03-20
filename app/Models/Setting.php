<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\Loggable;

class Setting extends Model
{
    use Loggable;
    protected $fillable = ['key', 'value'];

    /**
     * Helper untuk mengambil value berdasarkan key dengan Cache
     * supaya tidak terus-menerus membebani database.
     */
    public static function get($key, $default = null)
    {
        return Cache::rememberForever('setting_' . $key, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Helper untuk menyimpan atau update setting sekaligus hapus cache
     */
    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('setting_' . $key);

        return $setting;
    }
}
