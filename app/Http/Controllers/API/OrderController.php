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
use App\Http\Controllers\BaseController;
use App\Http\Resources\DepatmentOrderDetailResource;

class OrderController extends BaseController
{

    public function createOrder(Request $request){

        $user_id = auth()->user()->id;
        $input = $request->all();

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
                        $is_available = Item::where('id',$itemId)->where('is_available',1)->first();
                       if($is_available){
                       
                            //change table's status
                            $status = Table::where('id',$input['table_id'])->first()->status;
                            if($status == "available"){
                                $table = Table::where('id',$input['table_id'])->first();
                                $table->status = "in_use";
                                $table->save();
                            }
                            // else{
                                
                            //         return $this->sendError("This table not available");
                                
                            // }

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
                            $sell_price_item = Item::where('id',$itemId)->where('is_available',1)->first()->sell_price;

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

                $order_total_cost = 0 ;
                foreach($orderDetails as $orderDetail){
                    $total_price_order_detail = $orderDetail->total_price;
                    $order_total_cost += $total_price_order_detail;
                }
               
                $order->consumption_taxs = $order_total_cost * 0.1;
                $order->local_adminstration = $order_total_cost * 0.05;
                $order->rebuild_tax = $order_total_cost * 0.01;
                $order->taxes = ($order_total_cost * 0.1) + ($order_total_cost * 0.05) + ($order_total_cost * 0.01);
                $order->total_after_taxes = ($order_total_cost * 0.1) + ($order_total_cost * 0.05) + ($order_total_cost * 0.01) + $order->total_cost;
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
        
        $data['orders'] = OrderResource::collection($orders);

        return $this->sendResponse($data, 'All orders have returned successfully');

    }

    public function prepareItems(Request $request){

        $user_id = auth()->user()->id;

        $validator = Validator::make($request->all(),
        [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $order = Order::where('id',$input['order_id'])->first();
        $order_id = $order->id;

        if($order->status == "pending"){

            $items = Item::whereHas('orderDetails', function ($q) use($order_id) {
                    $q->where('order_id',$order_id);
                })->get();

            if(count($items) > 0){
                foreach($items as $item){

                    if($item->category){
                        $department = $item->category->department;

                        $orderDetail = OrderDetail::where('order_id',$order_id)->where('item_id',$item->id)->where('status',"pending")->first();

                        //recieve the department the order to prepare
                        $depatmentOrderDetail = new DepatmentOrderDetail();
                        $depatmentOrderDetail->order_detail_id = $orderDetail->id;
                        $depatmentOrderDetail->department_id = $department->id ;
                        $depatmentOrderDetail->status = "ready";
                        $depatmentOrderDetail->save();

                        //recieve the chief the order to prepare
                        $departmentUser = DepartmentUser::where('department_id',$department->id)->first();
                        if($departmentUser){
                            $departmentUser->status = "active";
                            $departmentUser->save();
                        }
                        // else{
                        //     return $this->sendError("This department dont have chief");
                        // }

                        return $this->sendResponse(new DepatmentOrderDetailResource($depatmentOrderDetail), 'The order has sent to prepare successfully');
                        
                    }
                }

            }

        }
        else{
            return $this->sendError("The order must be pending");
        }


    }
}
