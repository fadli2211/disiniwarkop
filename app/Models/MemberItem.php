<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'point',
    ];

     protected $casts = [
        'menu_id' => 'integer', // force to int
    ];


    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
