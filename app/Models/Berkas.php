<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Berkas extends Model
{
    use SoftDeletes, HasAuditLog;
    protected $fillable = [
        'modul',
        'nomor_registrasi',
        'subjek',
        'tanggal_kejadian',
        'metadata',
        'user_id', // Pastikan ini ada untuk relasi dengan User
    ];
    // Cast metadata agar mudah diakses sebagai array
    protected $casts = [
        'metadata' => 'array',
        'tanggal_kejadian' => 'date',
    ];

    // Relasi ke banyak file


    // Scope untuk memfilter modul
    public function scopeModul($query, $modul)
    {
        return $query->where('modul', $modul);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id'); // Pastikan Anda memiliki kolom user_id
    }

    public function files()
    {
        return $this->hasMany(BerkasFile::class, 'berkas_id'); // Sesuaikan dengan nama model file Anda
    }
}
