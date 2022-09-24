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
use App\Models\DepatmentOrderDetail;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Http\Resources\OrderResource;
use App\Http\Controllers\BaseController;
use App\Http\Resources\DepatmentOrderDetailResource;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\ReceiveItemsByDepartmentRequest;
use DB;

class OrderController extends BaseController
{

    //create order from items to table - by captain 
    public function createOrder(CreateOrderRequest $request){
       
        $user_id = auth()->user()->id;
        $input = $request->all();
        DB::beginTransaction();
        try{
          
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
                 else{
                    $order->payment_method = "";	
                 }
                 $order->user_id = $user_id;
                 $order->client_name = auth()->user()->name;

                 if(isset($input['notes'])){
                     $order->notes = $input['notes'];	
                 }

                 $order->save();
                 $order_total_cost = 0 ;
                foreach($itemIds as $key=>$itemId){

                    if(Item::find($itemId)){
                        $is_available = Item::where('id',$itemId)->where('is_available',1)->first();
                       if($is_available){
                       
                            //change table's status
                            $status = Table::where('id',$input['table_id'])->first()->status;
                            if($status == "available"){
                                $table = Table::where('id',$input['table_id'])->first();
                                $table->status = "in_use";
                                $table->save();
                            }

                            //store order details
                            $sell_price_item = Item::where('id',$itemId)->where('is_available',1)->first()->sell_price;

                            $orderDetail = new OrderDetail();
                            $orderDetail->order_id = $order->id;
                            $orderDetail->item_id  = $itemId;
                            $orderDetail->status  = "pending";
                            $orderDetail->cost  = $sell_price_item;
                            $orderDetail->total_price  = $sell_price_item * $countItems[$key];
                            $order_total_cost += $orderDetail->total_price;
                            $orderDetail->save();
                            
                            $orderDetails[] = $orderDetail;
                        }
                        else{
                            DB::rollback();
                            return $this->sendError("The item $itemId is not available");
                        }
                    }
                    else{
                        DB::rollback();
                        return $this->sendError("The item $itemId not exist");
                    }

                }


                //recalculate  total price and taxes
                $order->total_price = $order_total_cost;
                $order->consumption_taxs = $order_total_cost * 0.1;
                $order->local_adminstration = $order_total_cost * 0.05;
                $order->rebuild_tax = $order_total_cost * 0.01;
                $order->taxes = ($order_total_cost * 0.1) + ($order_total_cost * 0.05) + ($order_total_cost * 0.01);
                $order->total_after_taxes = ($order_total_cost * 0.1) + ($order_total_cost * 0.05) + ($order_total_cost * 0.01) + $order->total_cost;
                $order->save();
                DB::commit();

                return $this->sendResponse(new OrderResource($order), 'The order has created successfully');
            }
            else{
                            DB::rollback();

                return $this->sendError("You did not insert any item");
            }

        }
        else{
            DB::rollback();

            return $this->sendError("item_ids is not array");
        }

        }catch(Exception $e){
            DB::rollback();
        }
        
    }

    //show orders and order details including calculated fields -by captain
    public function orders(){

        $user_id = auth()->user()->id;
        $orders = Order::all();

        return $this->sendResponse(OrderResource::collection($orders), 'All orders have returned successfully');

    }

    //department receive his own items to prepare - by chief
    public function receiveItemsByDepartment(ReceiveItemsByDepartmentRequest $request){

        $user_id = auth()->user()->id;

        $input = $request->all();

        $department = Department::where('id',$input['department_id'])->first();
        $items =  $department->items;
        $itemsIds = $items->pluck('id')->toArray();    
        $orders = Order::whereHas('orderDetails', function ($q) use($itemsIds) {
            $q->whereIn('item_id',$itemsIds);
        })->where('status',"pending")->get();
      
        $data['orders']  = OrderResource::collection($orders);

        return $this->sendResponse($data,'All orders returned successfully');


    }
}
