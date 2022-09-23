<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Http\Resources\ItemResource;
use App\Http\Resources\OrderResource;

class OrderDetailResource extends JsonResource
{

    public function toArray($request)
    {

        $notes = "";
        if($this->notes){
            $notes = $this->notes;
        }

        $client_name = "";
        if($this->client_name){
            $client_name = $this->client_name;
        }
        $discount_amount= 0.0;
        if($this->$discount_amount){
            $discount_amount = $this->$discount_amount;
        }

        $payment_method= "";
        if($this->payment_method){
            $payment_method = $this->$payment_method;
        }
    
        return [

            'id' => $this->id,
            'order_id ' => $this->order_id ,
            'item_id' => $this->item_id ,
            'total_price' => $this->total_price,
            'count' => $this->count ,
            'is_fired' => $this->is_fired,
            'status' => $this->status,
            'notes' => $this->notes	,
            'note_price' => $this->note_price ,
            'delay' => $this->delay,
            'cost' => $this->cost,

        ];

    }

}

