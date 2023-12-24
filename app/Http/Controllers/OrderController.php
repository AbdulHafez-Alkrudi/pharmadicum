<?php

namespace App\Http\Controllers;

use App\Models\{Order, OrderItem, OrderStatus, Role};
use Carbon\Exceptions\UnknownSetterException;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{Auth, DB, Validator};

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

        // here if the order status is Delivered, the order can't
        // be changed anymore
        if($order->order_status_id == OrderStatus::DELIVERED){
            return $this->sendError("this order has delivered already and couldn't be updated anymore");
        }
        $order->update($request->except('lang'));

        return $this->get_order($id);
    }
    protected function get_order($id = null)
    {
        $user = Auth::user();
        $is_admin = $user->role_id == Role::ADMIN;

        $order = Order::query()
            ->when(request('lang') == 'ar' ,
                function($query) use ($user , $is_admin) {
                    return $query
                        ->select("id" , "customer_id" , "order_status_id", "payment_status_id", "total_invoice" , "created_at")
                        ->with([
                            'user:id,pharmacy_name',
                            'items:id,order_id,medicine_id,amount,unit_price' ,
                            'order_status:id,name_AR as name' ,
                            'payment_status:id,name_AR as name'
                        ])
                        // here if the user isn't the admin i wanna send all the orders
                        ->when(!$is_admin , function($query) use($user){
                            return $query -> where('customer_id' , $user->id);
                        });
                },
                function($query) use ($user, $is_admin) {
                    return $query
                        ->select("id" , "customer_id" , "order_status_id", "payment_status_id", "total_invoice" , "created_at")
                        ->with([
                            'user:id,pharmacy_name',
                            'items:id,order_id,medicine_id,amount,unit_price',
                            'order_status:id,name_EN as name' ,
                            'payment_status:id,name_EN as name',
                        ])
                        ->when(!$is_admin , function($query) use($user){
                            return $query
                             ->where('customer_id' , $user->id);
                        });
                }
            )
            ->OrderBy('order_status_id')
            // here I'm checking if i wanna retrieve a specific order or all the orders
            ->when($id == null ,
                    function($query){
                        return $query->get();
                    },
                    function($query) use ($id) {
                        if(request('lang') == 'ar')
                            return $query
                                -> with([
                                    'items.medicine:id,category_id,scientific_name_AR as scientific_name,economic_name_AR as economic_name,unit_price' ,
                                    'items.medicine.category:id,name_AR as name'
                                    ])
                                ->find($id);
                        return $query
                            ->with([
                                'items.medicine:id,category_id,scientific_name_EN as scientific_name,economic_name_EN as economic_name,unit_price',
                                'items.medicine.category:id,name_EN as name'
                            ])
                            ->find($id);
                    }
            );

        if(!is_null($id)) {
            $items = $order['items'];
            $items->map(function($item){
                $item['total_price'] = $item['unit_price'] * $item['amount'];
                return $item ;
            });
        }
        return $this->sendResponse($order , 'orders');
    }
}

