<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Http\Resources\OrderDetailResource;


class OrderResource extends JsonResource
{

    public function toArray($request)
    {

        $orderDetails = $this->orderDetails;

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
            'table_id' => $this->table_id ,
            'order_date' => $this->order_date,
            'total_price' => $this->total_price,
            'payment_state' => $this->payment_state ,
            'payment_method' => $payment_method,
            'status' => $this->status,
            'customer' => $this->customer,
            'user_id' => $this->user_id ,
            'total_cost' => $this->total_cost,
            'total_after_taxes' => $this->total_after_taxes,
            'taxes' => $this->taxes,
            'consumption_taxs' => $this->consumption_taxs,
            'local_adminstration' => $this->local_adminstration,
            'rebuild_tax' => $this->rebuild_tax,
            'notes' => $notes,
            'client_name' => $client_name,
            'discount_amount' => $discount_amount,
            'orderDetails' => OrderDetailResource::collection($orderDetails),

        ];

    }

}

