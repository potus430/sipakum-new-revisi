<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';

    protected $fillable = [
        'judul',
        'pelapor',
        'terlapor',
        'jenis_pengaduan',
        'sarana_pengaduan',
        'isi_pengaduan', // Tetap dipertahankan sebagai detail pengaduan
        'status_pengaduan',
        'tindak_lanjut',
        'keterangan',
        'anonim',
        'file_pendukung',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(PengaduanLog::class);
    }
}
