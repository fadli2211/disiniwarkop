<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'status', 'code'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_code', 'code');
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            0 => 'Kosong',
            1 => 'Dipakai',
            2 => 'Telah digunakan',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            0 => 'success',
            1 => 'primary',
            2 => 'danger',
            default => 'dark',
        };
    }
}
