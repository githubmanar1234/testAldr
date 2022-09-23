<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Item;
use App\Models\Table;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{

    public function createOrder(Request $request){

        $user_id = auth()->user()->id;

        $validator = Validator::make($request->all(),
        [
            'table_id' => 'required|exists:tables,id',
            'item_ids' => 'required',
            'count_items' => 'required',
            'customer' => 'numeric',
            'payment_method' => 'in:card,cash,city_ledger,voucher,credit',
            'notes' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $itemIds = $input['item_ids'];
        $itemIds = json_decode($itemIds ,true);

        $countItems = $input['count_items'];
        $countItems = json_decode($countItems ,true);

        if(count($itemIds) !== count($countItems)){
            return $this->sendError("itemIds array must be equal countItems array");
        }

        $orderDetails = [];

        if (is_array($itemIds)){
            if (isset($itemIds[0])){
                foreach($itemIds as $key=>$itemId){
                    if(Item::find($itemId)){
                        $is_available = Item::where('is_available',1)->first();
                       if($is_available){
                       
                            //change table's status
                            $status = Table::where('id',$input['table_id'])->first()->status;
                            if($status == "available"){
                                $table = Table::where('id',$input['table_id'])->first();
                                $table->status = "in_use";
                                $table->save();
                            }

                            //create new order
                            $order = new Order();
                            $order->table_id = $input['table_id'];
                            $order->order_date = Carbon::now();
                            $order->table_id = $input['table_id'];
                            $order->status = "pending";	
                            if(isset($input['customer'])){
                                $order->customer = $input['customer'];	
                            }
                            if(isset($input['payment_method'])){
                                $order->payment_method = $input['payment_method'];	
                            }
                            $order->user_id = $user_id;
                            $order->client_name = auth()->user()->name;

                            if(isset($input['notes'])){
                                $order->notes = $input['notes'];	
                            }

                            $order->save();

                            //store order details
                            $sell_price_item = Item::where('is_available',1)->first()->sell_price;

                            $orderDetail = new OrderDetail();
                            $orderDetail->order_id = $order->id;
                            $orderDetail->item_id  = $itemId;
                            $orderDetail->item_id  = $itemId;
                            $orderDetail->status  = "pending";
                            $orderDetail->cost  = $sell_price_item;
                            $orderDetail->total_price  = $sell_price_item * $countItems[$key];
                            $orderDetail->save();

                            $orderDetails[] = $orderDetail;
                        }
                        else{
                            return $this->sendError("The item $itemId is not available");
                        }
                    }
                    else{
                        return $this->sendError("The item $itemId not exist");
                    }

                }

                foreach($orderDetails as $orderDetail){
                    $total_price_order_detail = $orderDetail->total_price;
                    $order_total_cost += $total_price_order_detail;
                }
                $order->total_cost = $order_total_cost;
                $order->consumption_taxs = $order_total_cost * 0.1;
                $order->local_adminstration = $order_total_cost * 0.05;
                $order->rebuild_tax = $order_total_cost * 0.01;
                $order->taxes = ($order_total_cost * 0.1) + ($order_total_cost * 0.05) + ($order_total_cost * 0.01);
                $order->save();
                $order->total_after_taxes = $order->taxes + $order->total_cost;
                $order->save();

                return $this->sendResponse(new OrderResource($order), 'The order has created successfully');
            }
            else{
                return $this->sendError("You did not insert any item");
            }

        }
        else{
            return $this->sendError("item_ids is not array");
        }

    }

    public function orders(){

        $user_id = auth()->user()->id;
        $orders = Order::all();

        return $this->sendResponse(OrderResource::collection($orders), 'All orders have returned successfully');

    }
}