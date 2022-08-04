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
            
        $data=getDetails($request);
        // $data['order_quantity'] =getQuantity();
        // $data['total_price'] =getTotalPrice();
        return response(prepareResult(true, $data, trans('translate.fetched_records')), 200 , ['Result'=>'Orders Data']);
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
