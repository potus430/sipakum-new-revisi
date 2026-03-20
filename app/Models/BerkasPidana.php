<?php

namespace App\Models;

//use App\Traits\HasAuditLog; // Pastikan Trait ini sudah dibuat
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class BerkasPidana extends Model
{
    use HasFactory, Loggable, SoftDeletes;

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'jenis',
        'no_perkara',
        'pihak',
        'pasal',
        'tgl_putus',
        'tgl_penyerahan',
        'created_by',
        'updated_by',
    ];

    /**
     * Casting otomatis untuk tanggal agar menjadi objek Carbon.
     */
    protected function casts(): array
    {
        return [
            'tgl_putus' => 'date',
            'tgl_penyerahan' => 'date',
        ];
    }
    //
}
