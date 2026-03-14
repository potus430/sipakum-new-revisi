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
        'isi_pengaduan',
        'anonim',
        'status',
        'file_pendukung',
        'user_id',
        'status_lama',
        'status_baru',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs() {
    return $this->hasMany(PengaduanLog::class);
}
}