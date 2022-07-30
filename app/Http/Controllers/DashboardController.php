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
            // $data['todaySale'] = DB::table('order_contains')->whereDate('order_contains.created_at', '=', date("Y-m-d"))->sum('quantity');
            // $data['orders'] = OrderContains;
            // $data['totalQuantity'] = OrderContain::where('product_menu_id')->sum('quantity')
             
            foreach(OrderContain::all('name') as $name){
            $totalQuantity = OrderContain::where('name', $name);
            $totalPrice = OrderContain::where('product_menu_id', $name)->sum('netPrice');
            $todaySale = DB::table('order_contains')->whereDate('order_contains.created_at', '=', date("Y-m-d"))->sum('quantity');
            // return $data['name']->get();
            // return $data['totalQuantity']->get();
            // $data['todayorderSale']= DB::table('order_contains')->where('order_contains.product_menu_id', '==', $data['name'])->sum('quantity');
            $data[] = $totalQuantity;
            $data[] = $totalPrice;
            $data[] = $todaySale;
        }
        return $data;

            
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
            if(!empty($request->end_date))
            {
                $data->where('order_status', $request->order_status)->whereDate('created_at', '=', $request->end_date);
            }
           
            if(!empty($request->per_page_record))
            {
                $perPage = $request->per_page_record;
                $page = $request->input('page', 1);
                $total = $data->count();
                $result = $data->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                $data = $pagination;
            }
            else
            {
                $data = $data->get();
            }

            return response(prepareResult(true, $data, trans('translate.fetched_records')), 200 , ['Result'=>'Your data has been saved successfully']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }
        public function dashboardGraph()
    {
        try {
            $data = [];
            $data['total_sale'] =getLast30TotalSale();
            $data['total_customer'] =getLast30TotalCustomer();
            $data['total_product'] =getLast30TotalProduct();
            $data['total_revenue'] =getLast30TotalRevenue();
            $data['labels'] =getLast30DaysList();
            return response(prepareResult(true, $data, trans('translate.fetched_records')), 200 , ['Result'=>'Graph Data']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }

    }

    
}
