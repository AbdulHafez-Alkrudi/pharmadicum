<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ExpirationMedicine;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
   public function __invoke()
   {
       $report = array();
       $beginning_of_last_month = Carbon::now()->/*subMonth()->*/startOfMonth();
       $ending_of_last_month = Carbon::now()->/*subMonth()->*/endOfMonth();
       $categories_all = Category::query()->
               when(request('lang') == 'ar' ,
                   fn($query) => $query->select('id' , 'name_AR as name'),
                   fn($query) => $query->select('id' , 'name_EN as name')
               )->with([
                   'medicines:id,category_id',
                   'medicines.batches:id,medicine_id,amount'
               ])
                   ->get();


       // 1- total number of users
       $report['total_user'] = User::query()->count();

       // 2-total number of the new users last month
       $report['registered_users_last_month'] = User::whereBetween('created_at' , [$beginning_of_last_month , $ending_of_last_month])->count();

       // 3-total number of the medicines in stock (the number of different types)
       $report['total_medicines_types'] = Medicine::query()->count();

       // 4-total amount of the medicine in stock: (the total number of all medicines)
       $total_amount_medicines = ExpirationMedicine::query()->sum('amount');
       $report['total_amount_in_stock'] = (integer)$total_amount_medicines;


       // 5-total amount of the sold medicines last month
       $total_amount_sold_medicines = OrderItem::query()->
                whereBetween('created_at' , [$beginning_of_last_month , $ending_of_last_month])
                ->sum('amount');

       $report['total_amount_sold_medicines'] = $total_amount_sold_medicines;



       // 6-total invoices in the last month

       $report['total_invoices'] = Order::query()
               ->whereBetween('created_at' , [$beginning_of_last_month , $ending_of_last_month])

               ->sum('total_invoice');

       // 7-Category percentage in the stock


      $categories = $categories_all ;
       // here i'm looping over each category and count how many medicines exists in the stock from that category
       $categories = $categories->map(function($category) use ($total_amount_medicines){
            // here from each medicine i'll get an array contains the amount, and it's a bit hard to work with that,
           //however, from each medicine i just need one info from it which is the total amount,
           // so instead of putting 'for each medicine' its amount in a separate array, i'll combine all of them in one array using flatMap function


           $total_amount = $category->medicines->flatMap(function($medicine){
               return $medicine->batches->pluck('amount');
           })->sum();

           $category['percentage'] = $total_amount / $total_amount_medicines;

           return [
              'category_name' => $category['name'],
              'percentage' => $category['percentage']
           ];
       });

       $report['category_percentage_in_the_stock'] = $categories ;

       // 8- Category percentage for the sold medicines last month

        $categories = OrderItem::query()->select('medicines.category_id', DB::raw('SUM(amount) as total'))
            ->join('medicines', 'medicines.id', '=', 'order_items.medicine_id')
            ->whereBetween('order_items.created_at' , [$beginning_of_last_month , $ending_of_last_month])
            ->groupBy('medicines.category_id')
            ->get();

        $categories->map(function($category) use($total_amount_sold_medicines){
           $category['percentage'] = $category['total']/$total_amount_sold_medicines ;
        });
        $report['category_percentage_for_sold_medicines'] = $categories;

        // 9- the amount of sold medicines last month
        $report['amount_of_orders_last_month'] = Order::query()
            ->whereBetween('created_at' , [$beginning_of_last_month , $ending_of_last_month])
            ->count();

        // 10- Number of orders in each week:

        $orders_in_each_week = Order::query()
              ->select(DB::raw('WEEK(created_at) as week , count(*) as number_of_orders'))
              ->whereBetween('created_at' , [$beginning_of_last_month , $ending_of_last_month])
              ->groupBy('week')
              ->get();
        $cnt = 1 ;
        foreach($orders_in_each_week as $week){
            $week['week'] = $cnt++;
        }
        $report['orders_in_each_week'] = $orders_in_each_week ;

        // 11-the most sold medicine last month
       $the_most_sold_medicine= OrderItem::query()
            ->select('medicine_id' , DB::raw('SUM(amount) as total_sum '))
            ->whereBetween('created_at' , [$beginning_of_last_month , $ending_of_last_month])
            ->groupBy('medicine_id')
            ->orderBy('total_sum' , 'DESC')
            ->first();
       ;
        if(!is_null($the_most_sold_medicine))
           $report['the_most_sold_medicine'] = (new MedicineController)->get_medicine(request('lang') , $the_most_sold_medicine['medicine_id']);
        else
            $report['the_most_sold_medicine'] = -1 ;
       //12-Number of sold medicines in each category:

       $report['sold_medicines_each_category'] = OrderItem::query()
            ->select('medicines.category_id' , DB::raw("SUM(order_items.amount) as total_sum"))
            ->join('medicines' , 'medicines.id' , '=' , 'order_items.medicine_id')
            ->groupBy('medicines.category_id')
            ->get();
       return $this->sendResponse($report , 'report');
   }
}
