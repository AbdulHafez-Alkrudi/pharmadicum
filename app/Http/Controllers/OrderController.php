<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use Carbon\Exceptions\UnknownSetterException;use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->get_order();
    }
    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all() , [
            'total_invoice' => ['required'] ,
            'items' => ['array' , 'present'] ,
            'items.*.medicine_id' => ['required'] ,
            'items.*.amount' => ['required'] ,
            'items.*.unit_price' => ['required'] ,

        ]);
        // here if the validation failed i'll rollback the transaction
        if($validator->fails()){
            DB::rollBack();
            return $this->sendError($validator->errors());
        }
        $order = Order::create([
            'customer_id' => auth()->id() ,
            'total_invoice' => $request['total_invoice']
        ]);
        foreach($request['items'] as $item){
                OrderItem::create([
                    'order_id' => $order->id ,
                    'medicine_id' => $item['medicine_id'] ,
                    'amount' => $item['amount'],
                    'unit_price' => $item['unit_price']
                ]);
        }
        DB::commit();
       return $this->get_order($order->id);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->get_order($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if(!Order::where('id' , $id)->exists()){
            return $this->sendError("the order id isn't valid");
        }
        $order = Order::find($id);

        // here if the order status id == 1 means the order is bending, otherwise the customer received his order and can't
        // change it anymore
        if($order->order_status_id == 3){
            return $this->sendError("this order couldn't be updated anymore");
        }
        $order->update($request->except('lang'));

        return $this->get_order($id);
    }

    protected function get_order($id = null)
    {
        $user = Auth::user();
        $order = Order::query()
            ->when(request('lang') == 'ar' ,
                function($query) use ($user) {
                    return $query
                        ->select("id" , "customer_id" , "order_status_id", "payment_status_id", "total_invoice" , "created_at")
                        ->with([
                            'items:id,order_id,medicine_id,amount,unit_price' ,
                            'order_status:id,name_AR as name' ,
                            'payment_status:id,name_AR as name'
                        ])
                        ->where("customer_id" , $user->id);
                },
                function($query) use ($user) {
                    return $query
                        ->select("id" , "customer_id" , "order_status_id", "payment_status_id", "total_invoice" , "created_at")
                        ->with([
                            'items:id,order_id,medicine_id,amount,unit_price' ,
                            'order_status:id,name_EN as name' ,
                            'payment_status:id,name_EN as name',
                        ])
                        ->where("customer_id" , $user->id);
                }
            )
            ->when($id == null ,
                    function($query){
                        return $query->get();
                    },
                    function($query) use ($id) {
                        return $query->find($id);
                    }
            );
        return $this->sendResponse($order , 'orders');
    }
}

