<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductMenu;
use App\Models\Order;
use App\Models\Employee;
use App\Models\AttendenceList;
use App\Models\OrderContain;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;



class DashboardController extends Controller
{
    public function dashboard()
    {
        try {
            $data = [];
          
            $data['todaySale'] = DB::table('orders')->whereDate('orders.created_at', '=', date("Y-m-d"))->sum('netAmount');
            $data['employeePresentToday'] = AttendenceList::where('attendence',2)->whereDate('created_at', '=', date("Y-m-d"))->count();
            $data['totalEmployee'] = Employee::count();

           

            return response(prepareResult(true, $data, trans('translate.fetched_records')), 200 , ['Result'=>'Your data has been saved successfully']);
            } 
            catch (\Throwable $e) {
                Log::error($e);
                return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
            }
    }

    public function orderList(Request $request)
    {
        try {
            $data = [];
            $data['todaySale'] = DB::table('order_contains')->whereDate('order_contains.created_at', '=', date("Y-m-d"))->sum('quantity');
           
            $data['totalPrice'] = OrderContain::sum('netPrice');
            // $data['totalQuantity'] = OrderContain::where('product_menu_id')->sum('quantity');
            $data['name'] = OrderContain::select('*');
            $data['todayorderSale']= DB::table('order_contains')->where('order_contains.product_menu_id', '==', OrderContain::select('product_menu_id'))->sum('quantity');
            
            foreach($data as $data){
                $productMenuItem = ProductMenu::find( $data['product_menu_id']);
                
                $data = new OrderContain;
                $data->product_menu_id = $data['product_menu_id'];
                $data->category_id = $data['category_id'];

                // below data is from another table
                // $addorder->name = $productMenuItem->name;
                $data->name = $data['name'];
                $data->quantity = $data['quantity'];

                 // below data is from another table
                // $addorder->price = $productMenuItem->price;
                $data->price = $data['price'];
                // $addorder->netPrice = $order['quantity'] * $productMenuItem->price ;
                $data->netPrice = $data['netPrice'];
                $data->save();
                
            }
 
                // database sum querry
            // $quantitySum= DB::table('order_contains')->where('order_contains.order_id', $info->id)->sum('quantity');
            // $amountSum= DB::table('order_contains')->where('order_contains.order_id', $info->id)->sum('netPrice');

            // $info = Order::find( $info->id);
            // $info->cartTotalQuantity = $quantitySum;
            // $info->cartTotalAmount = $amountSum;
            // $info->taxes = $request->taxes;
            // $info->netAmount = $amountSum + $request->taxes;
            // $info->save();

             DB::commit();
             $data['order_contains'] = $data->orderContains;
           
                    // ->with('orderContains')
                    // ->orderBy('id', 'desc');

            // if(!empty($request->id))
            // {
            //     $query->where('id', $request->id);
            // }
            // if(!empty($request->table_number))
            // {
            //     $query->where('table_number', $request->table_number);
            // }
            // if(!empty($request->order_status))
            // {
            //     $query->where('order_status', $request->order_status);
            // }
            // if(!empty($request->created_at))
            // {
            //     $query->where('created_at', $request->created_at);
            // }
           
            // date wise filter from here
            // if(!empty($request->end_date))
            // {
            //     $data['name']->where('order_status', $request->order_status)->whereDate('created_at', '=', $request->end_date);
            // }
           
            // // if(!empty($request->per_page_record))
            // // {
            // //     $perPage = $request->per_page_record;
            // //     $page = $request->input('page', 1);
            // //     $total = $query->count();
            // //     // $result = $data['name']->offset(($page - 1) * $perPage)->limit($perPage)->get();


            return response()->json(prepareResult(true, $data, trans('translate.created')), 200 , ['Result'=>'Your data has been saved successfully']);
        }
        
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }
        public function dashboardGraph(Request $request)
    {
        try {
            $data = [];
            // $data['category_name'] =getCategoryName($request->categoryDay);
            // $data['category_quantity'] =getCategoryQuantity($request->categoryDay);
            $data['total_sale'] =getLast30TotalSale($request->day , $request->startDate, $request->endDate);
            // // $data['total_customer'] =getLast30TotalCustomer();
            $data['total_product'] =getLast30TotalProduct($request->day , $request->startDate, $request->endDate);
            $data['total_revenue'] =getLast30TotalRevenue($request->day , $request->startDate, $request->endDate);
            $data['labels'] =getLast30DaysList($request->day , $request->startDate, $request->endDate);
            return response(prepareResult(true, $data, trans('translate.fetched_records')), 200 , ['Result'=>'Graph Data']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }

    }

    
}
