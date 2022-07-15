<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\UnitData;
use App\Models\ProductStockManage;
use App\Models\ProductMenu;
use App\Models\ProductInfo;
use App\Models\EmployeeAttendence;
use App\Models\AttendenceList;

class UserInfo extends Controller
{
    //
    function checkConnection()
    {
        // echo "hello";
        return ['Status'=>'Connection working fine'];
        // data is stored in from of array
    }

    function addData(Request $req)
    {

        //    form validation querry
        $req->validate(
            [
                'name'=>'required',
                'mobile'=>'required|numeric|digits_between:10,10',
                'email'=>'required|email',
                'pincode'=>'required|numeric|digits_between:6,6',
            ]
        );
        // echo '<pre>';
        //    print_r($req->all());



        //insert data into DB querry 
        $info = new UserData;
        $info->name =$req['name'];
        $info->mobile =$req['mobile'];
        $info->email =$req['email'];
        $info->address =$req['address'];
        $info->status =$req['status'];
        $info->pincode =$req['pincode'];
        $result=$info-> save();
        if($result)
        {
            return ['Result'=>'Your data has been saved successfully'];
        }else
        {
            return ['Result'=>'Your data has not been saved'];
        }
        
    }

    function displayData($id=null)
    {
        // return ['Result'=>'Your id is '.$id];
        // above is to debug id number


        return $id?UserData::find($id):UserData::all();
        // above logic say, if $id is available than find with respect to $id else display all
    }


    public function temp(Request $request)
    {
        // $old = ProductMenu::where('product_menus.id', $order['product_menu_id'])->get();
        // $updateStock = ProductInfo::find( $request->product_id);
            // $updateStock->current_quanitity = $quantitySum;
    //         $old = ProductMenu::find( $request->product_id);
    //    return $old->product;
        // $new = 10;
        // $old = ProductInfo::where('product_infos.id', $request->product_id)->get('current_quanitity')->first();
        // // $old= DB::table('product_infos')->where('product_infos.id', $request->product_id)->get('current_quanitity')->first();

        // // return $old;
        // return $old->current_quanitity + $new;
        // // $line_cost =(int)$price * (int)$quantity['key'];
    }
}
