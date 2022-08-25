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

           

            return response(prepareResult(true, $data, trans('Record Fatched Successfully')), 200 , ['Result'=>'Your data has been saved successfully']);
            } 
            catch (\Throwable $e) {
                Log::error($e);
                return response()->json(prepareResult(false, $e->getMessage(), trans('Error while fatching Records')), 500,  ['Result'=>'Your data has not been saved']);
            }
    }

    public function orderList(Request $request)
    {
        try {
            
        $data['order-details']= getDetails($request->day , $request->startDate, $request->endDate, $request->category);
        return response(prepareResult(true, $data, trans('Record Fatched Successfully')), 200 , ['Result'=>'Orders Data']);
    } 
    catch (\Throwable $e) {
        Log::error($e);
        return response()->json(prepareResult(false, $e->getMessage(), trans('Error while fatching Records')), 500,  ['Result'=>'Your data has not been saved']);
    }
        
    }
    public function dashboardGraph(Request $request){
        try {
            $data = [];
            // $data['category_name'] =getCategoryName($request->categoryDay);
            // $data['category_quantity'] =getCategoryQuantity($request->categoryDay);
            $data['total_sale'] =getLast30TotalSale($request->day , $request->startDate, $request->endDate);
            // // $data['total_customer'] =getLast30TotalCustomer();
            $data['total_product'] =getLast30TotalProduct($request->day , $request->startDate, $request->endDate);
            $data['total_expense'] =getLast30TotalExpense($request->day , $request->startDate, $request->endDate);
            $data['labels'] =getLast30DaysList($request->day , $request->startDate, $request->endDate);
            return response(prepareResult(true, $data, trans('Record Fatched Successfully')), 200 , ['Result'=>'Graph Data']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('Error while fatching Records')), 500,  ['Result'=>'Your data has not been saved']);
        }

    }

    public function dashboardGraphByName(Request $request){
        try {
            $data = [];

            $data['order_details'] =getLast30details($request->day , $request->startDate, $request->endDate);
            // $data['total_product'] = getLast30TotalProduct($request->day , $request->startDate, $request->endDate);
            // $data['total_sale'] = getLast30TotalSales($request->day , $request->startDate, $request->endDate);
            // // // $data['total_customer'] =getLast30TotalCustomer();
            // $data['total_product'] =getLast30TotalProduct($request->day , $request->startDate, $request->endDate);
            // $data['total_revenue'] =getLast30TotalRevenue($request->day , $request->startDate, $request->endDate);
            // $data['labels'] =getLast30DaysList($request->day , $request->startDate, $request->endDate);

            return response(prepareResult(true,  $data, trans('Record Fatched Successfully')), 200 , ['Result'=>'Graph Data']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('Error while fatching Records')), 500,  ['Result'=>'Your data has not been saved']);
        }

    }

}
