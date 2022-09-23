<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Department;
use App\Http\Resources\ItemResource;
use App\Http\Resources\OrderResource;

class DepatmentOrderDetailResource extends JsonResource
{

    public function toArray($request)
    {

        if($this->department_id){
            $department = Department::where('id',$this->department_id)->first();
            if($department){
                $department = $department->title;
            }
            else{
                $department = "";
            }
        }
       
        
        return [

            'id' => $this->id,
            'order_detail_id' => $this->order_detail_id ,
            'department_id' => $department,
            'status' => $this->status,

        ];

    }

}

