<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class SuratKuasa extends Model
{
    use HasFactory, Loggable;

    protected $table = 'surat_kuasa';

    protected $fillable = [
        'kategori_perkara',
        'berkas_id',
        'nomor_surat_kuasa',
        'tanggal_surat',
        'penerima_kuasa',
        'pemberi_kuasa',
        'jenis_kuasa',
        'file_path',
    ];

    /**
     * Relasi ke model Berkas.
     * Setiap Surat Kuasa dimiliki oleh satu Berkas perkara.
     */
    // public function berkas(): BelongsTo
    // {
    //     return $this->belongsTo(Berkas::class);
    // }

    /**
     * Casting tipe data kolom.
     * Memastikan 'tanggal_surat' diperlakukan sebagai objek Carbon/Tanggal.
     */
    protected $casts = [
        'tanggal_surat' => 'date',
    ];



}
