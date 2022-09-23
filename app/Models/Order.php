<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'table_id', 'order_date', 'total_price','payment_state','payment_method','status','customer','user_id',
        'total_cost','total_after_taxes','discount_amount','taxes','consumption_taxs','local_adminstration','rebuild_tax','notes','client_name',
    ];

    public function orderDetails(){

        return $this->hasMany(OrderDetail::class,'order_id');
    }

    public function table(){

        return $this->belongsTo(Table::class, 'table_id','id');
    }

    public function user(){

        return $this->belongsTo(User::class, 'user_id','id');
    }

}
