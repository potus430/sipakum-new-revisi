<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class Gratifikasi extends Model
{
    use HasFactory, Loggable;

    protected $table = 'gratifikasi';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'pelapor',
        'pemberi',
        'bentuk_gratifikasi',
        'estimasi_nilai',
        'tanggal_penerimaan',
        'kronologi',
        'status',
        'file_bukti',
        'user_id', // Relasi ke staf yang menginput
    ];

    /**
     * Casting tipe data agar memudahkan pemrosesan di view.
     */
    protected $casts = [
        'tanggal_penerimaan' => 'date',
        'estimasi_nilai' => 'decimal:2',
    ];

    /**
     * Relasi ke model User (Staf yang melaporkan/menginput).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
