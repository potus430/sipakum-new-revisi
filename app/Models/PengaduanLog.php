<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;
class PengaduanLog extends Model
{
    use HasFactory, Loggable;

    // Menentukan nama tabel jika tidak mengikuti konvensi jamak standar Laravel
    protected $table = 'pengaduan_logs';

    /**
     * Properti $fillable mendefinisikan kolom-kolom yang
     * diperbolehkan untuk diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'pengaduan_id',
        'user_id',
        'status_lama',
        'status_baru',
        'created_at',
    ];

    /**
     * Relasi: Satu Log dimiliki oleh satu User (Admin yang melakukan perubahan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
