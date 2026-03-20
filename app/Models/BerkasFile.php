<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Traits\Loggable;

class BerkasFile extends Model
{
    use Loggable;
    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'berkas_id',
        'file_name', // Nama asli file (Contoh: Putusan_Pidana_01.pdf)
        'file_path', // Path tersimpan (Contoh: berkas/2026/03/uuid-file.pdf)
    ];

    /**
     * Mendapatkan pemilik dari file ini (relasi ke tabel berkas).
     */
    public function berkas(): BelongsTo
    {
        return $this->belongsTo(Berkas::class, 'berkas_id', 'id');
    }

    public function getIconAttribute()
    {
        if (Str::endsWith($this->file_path, '.pdf'))
            return 'document-text';
        if (Str::endsWith($this->file_path, ['.jpg', '.png']))
            return 'photo';
        return 'document';
    }
}
