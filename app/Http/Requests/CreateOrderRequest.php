<?php 
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest{
    public function rules():array
    {
        return [
            'table_id' => 'required|exists:tables,id',
            'item_ids' => 'required',
            'count_items' => 'required',
            'customer' => 'numeric',
            'payment_method' => 'in:card,cash,city_ledger,voucher,credit',
            'notes' => 'string'
        ];
    }
}





?>