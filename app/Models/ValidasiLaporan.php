<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidasiLaporan extends Model
{
    use HasFactory;

    protected $fillable = ['laporan_id', 'status', 'tanggal_validasi'];
}
