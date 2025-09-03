<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['uuid','user_id','name','phone','table_code','status'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            }
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

     public function table()
    {
        return $this->belongsTo(Table::class, 'table_code', 'code');
    }
}
