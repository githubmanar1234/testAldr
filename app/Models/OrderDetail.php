<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'order_id', 'item_id', 'total_price','count','is_fired','status','notes','note_price','delay','cost',
    ];

    public function item(){

        return $this->belongsTo(Item::class, 'item_id','id');
    }

    public function order(){

        return $this->belongsTo(Order::class, 'order_id','id');
    }
}
