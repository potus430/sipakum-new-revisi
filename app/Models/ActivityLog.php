<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang diasosiasikan dengan model.
     *
     * @var string
     */
    protected $table = 'activity_logs';

    /**
     * Atribut yang dapat diisi (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'module',
        'action',
        'target_id',
        'old_data',
        'new_data',
        'ip_address',
    ];

    /**
     * Cast atribut ke tipe data tertentu.
     * * Mengubah JSON dari database menjadi Array PHP secara otomatis
     * dan memudahkan manipulasi data saat ditampilkan.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Mendapatkan user yang melakukan aktivitas ini.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk mempermudah pencarian berdasarkan modul tertentu.
     */
    public function scopeModule($query, $moduleName)
    {
        return $query->where('module', $moduleName);
    }

    /**
     * Scope untuk mempermudah pencarian berdasarkan aksi tertentu.
     */
    public function scopeAction($query, $actionType)
    {
        return $query->where('action', strtoupper($actionType));
    }
}
