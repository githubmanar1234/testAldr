<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentUser extends Model
{
    use HasFactory,HasApiTokens,SoftDeletes;

    protected $fillable = [
        'department_id', 'user_id', 'status',
    ];

    public function department(){

        return $this->belongsTo(Department::class,'department_id','id');
    }

    public function user(){

        return $this->belongsTo(User::class,'user_id','id');
    }
}
