<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory,SoftDeletes;

    public function orderDetails(){

        return $this->hasMany(OrderDetail::class,'item_id');
    }

    public function category(){

        return $this->belongsTo(Category::class,'category_id','id');
    }
}
