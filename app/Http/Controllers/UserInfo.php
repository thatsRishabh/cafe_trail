<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Unit;
use App\Models\ProductStockManage;
use App\Models\ProductMenu;
use App\Models\ProductInfo;
use App\Models\EmployeeAttendence;
use App\Models\AttendenceList;
use App\Models\Recipe;
use App\Models\RecipeContains;

class UserInfo extends Controller
{
    //
    function checkConnection()
    {
        // echo "hello";
        return ['Status'=>'Connection working fine'];
        // data is stored in from of array
    }

    // function addData(Request $req)
    // {

    //     //    form validation querry
    //     $req->validate(
    //         [
    //             'name'=>'required',
    //             'mobile'=>'required|numeric|digits_between:10,10',
    //             'email'=>'required|email',
    //             'pincode'=>'required|numeric|digits_between:6,6',
    //         ]
    //     );
    //     // echo '<pre>';
    //     //    print_r($req->all());



    //     //insert data into DB querry 
    //     $info = new UserData;
    //     $info->name =$req['name'];
    //     $info->mobile =$req['mobile'];
    //     $info->email =$req['email'];
    //     $info->address =$req['address'];
    //     $info->status =$req['status'];
    //     $info->pincode =$req['pincode'];
    //     $result=$info-> save();
    //     if($result)
    //     {
    //         return ['Result'=>'Your data has been saved successfully'];
    //     }else
    //     {
    //         return ['Result'=>'Your data has not been saved'];
    //     }
        
    // }

    // function displayData($id=null)
    // {
    //     // return ['Result'=>'Your id is '.$id];
    //     // above is to debug id number


        // return $id?UserData::find($id):UserData::all();
        // above logic say, if $id is available than find with respect to $id else display all
    // }


    // public function temp(Request $request)
    // {
    //     // $old = ProductMenu::where('product_menus.id', $order['product_menu_id'])->get();
    //     // $updateStock = ProductInfo::find( $request->product_id);
    //         // $updateStock->current_quanitity = $quantitySum;
    //         $old = ProductMenu::find( $request->product_id);
    //    return $old->product;
        // $new = 10;
        // $old = ProductInfo::where('product_infos.id', $request->product_id)->get('current_quanitity')->first();
        // // $old= DB::table('product_infos')->where('product_infos.id', $request->product_id)->get('current_quanitity')->first();

        // // return $old;
        // return $old->current_quanitity + $new;

         // $today = getdate();

        // return $today['mon'];
    // }

    // public function temp1(Request $request)

    // {
        // $attendence = AttendenceList::whereIn('employee_id', $request->employee_id)->whereDate('created_at', date('Y-m-d'));
        // $attendence = AttendenceList::whereIn('employee_id', $request->employee_id)->whereBetween('created_at', date('Y-m-d'));

        // $attendence =AttendenceList::where('employee_id', $request->employee_id)->whereBetween('created_at', [$request->from_date.' 00:00:00', $request->end_date.' 23:59:59'])->get();
        // $attendence =AttendenceList::where('employee_id', $request->employee_id)->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->end_date)->get();
        // return $attendence;

        //  $attendence =AttendenceList::join('employee_attendences', 'attendence_lists.employee_id', '=', 'employee_attendences.id')
        //  ->join('product_infos', 'product_stock_manages.product_id', '=', 'product_infos.id')
        //  ->select('product_stock_manages.*', 'product_infos.name as product_infos_name', 'units.name as units_name' ,'product_infos.current_quanitity as product_infos_old_quantity',)
        //  ->orderBy('product_stock_manages.id', 'desc');

        //  $attendence = DB::table('attendence_lists')
        //  ->join('employee_attendences', 'attendence_lists.attendence_id', '=', 'employee_attendences.id')
        //  ->select('attendence_lists.*', 'employee_attendences.date as attendenceDate')
        //  ->where('employee_id', $request->employee_id)
        //  ->orderBy('attendence_lists.id', 'desc')->get();

        //    $attendence = AttendenceList::join('employee_attendences', 'attendence_lists.attendence_id', '=', 'employee_attendences.id')
        //  ->select('attendence_lists.*', 'employee_attendences.date as attendenceDate')
        //  ->where('employee_id', $request->employee_id)
        //  ->orderBy('attendence_lists.id', 'desc')->get();
        //  return $attendence->id;

        //  $journals = AttendenceList::where('employee_id', $request->employee_id);
        //     if(!empty($request->from_date) && !empty($request->end_date))
        //     {
        //         $journals->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->end_date);
        //     }
        //     elseif(!empty($request->from_date) && empty($request->end_date))
        //     {
        //         $journals->whereDate('created_at', '>=', $request->from_date);
        //     }
        //     elseif(empty($request->from_date) && !empty($request->end_date))
        //     {
        //         $journals->whereDate('created_at', '<=', $request->end_date);
        //     }
        //     $result= $journals->get();
        //     return $result;
    // }

        //     function getOVHours($time1, $time2, $ovtime1, $ovtime2)
        // {
        //     $converted_time1 = new DateTime($time1);
        //     $converted_time2 = new DateTime($time2);
        //     if($converted_time2 < $converted_time1)
        //     {
        //         $converted_time2 = $converted_time2->modify('+1 day');
        //     }

        //     $converted_ovtime1 = new DateTime($ovtime1);
        //     $converted_ovtime2 = new DateTime($ovtime2);

        //     if($converted_ovtime2 < $converted_ovtime1)
        //     {

        //         $converted_ovtime2 = $converted_ovtime2->modify('+1 day');
        //     }

        //     if(($converted_ovtime1 >= $converted_time1) && ($converted_ovtime2 <= $converted_time2))
        //     {
        //         $hours = getHours($ovtime1,$ovtime2,0);
        //         return $hours.'between';
        //     }
        //     elseif (($converted_ovtime1 <= $converted_time1) && ($converted_ovtime2 >= $converted_time2)) 
        //     {
        //         $hours = getHours($time1,$time2,0);
        //         return $hours.'beyond';
        //     }
        //     elseif (($converted_ovtime1 <= $converted_time1) && ($converted_ovtime2 <= $converted_time2)) 
        //     {
        //         $hours = getHours($time1,$ovtime2,0);
        //         return $hours.'before';
        //     }
        //     elseif (($converted_ovtime1 >= $converted_time1) && ($converted_ovtime2 >= $converted_time2)) 
        //     {
        //         $hours = getHours($ovtime1,$time2,1);
        //         return $hours.'after';
        //     }
        //     return $hours;
        // }

    //     public function temp1(Request $request)
    // {
    //     $query = RecipeContains::select('*')
    //         // ->join('product_menus', 'recipes.product_menu_id', '=', 'product_menus.id')
    //         // ->select('recipes.*','product_menus.name as product_menu_name' )
    //         //         ->with('recipeMethods:recipe_id,name,quantity,unit_id,product_info_stock_id,unit_name,unit_minValue')
    //                 ->where('recipe_id', 78)
    //                 ->orderBy('id', 'desc')->get();
                 


                    
    //                 // foreach ($query as $key => $recipe) {
    //                 //     $value=$recipe->unit_id
    //                 // };
    //         return $query;        
    // }
}
