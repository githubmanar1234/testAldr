<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepatmentOrderDetail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'department_order_details';

    protected $fillable = [
        'department_id', 'order_detail_id', 'status',
    ];

    public function department(){

        return $this->belongsTo(Department::class,'department_id','id');
    }

    public function orderDetail(){

        return $this->belongsTo(OrderDetail::class,'order_detail_id','id');
    }
}
