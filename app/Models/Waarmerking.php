<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class Waarmerking extends Model
{
    use HasFactory, Loggable;

    protected $table = 'waarmerking';

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'nomor_register',
        'nama_pemohon',
        //'jenis_dokumen',
        'tanggal_legalisasi',
        //'file_path',
        'catatan',
        'user_id',
    ];

    /**
     * Casting agar tanggal otomatis dikonversi ke objek Carbon.
     */
    protected $casts = [
        'tanggal_legalisasi' => 'date',
    ];

    // Tambahkan method ini agar relasi 'user' dikenali
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
