<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderContain;
use App\Models\ProductMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function searchOrder(Request $request)
    {
        try {
            $query = Order::select('*')
                    ->with('orderContains')
                    ->orderBy('id', 'desc');

            if(!empty($request->id))
            {
                $query->where('id', $request->id);
            }
            if(!empty($request->table_number))
            {
                $query->where('table_number', $request->table_number);
            }
            if(!empty($request->order_status))
            {
                $query->where('order_status', $request->order_status);
            }
            // if(!empty($request->created_at))
            // {
            //     $query->where('created_at', $request->created_at);
            // }
           
            // date wise filter from here
            if(!empty($request->end_date))
            {
                $query->where('order_status', $request->order_status)->whereDate('created_at', '=', $request->end_date);
            }
           
            if(!empty($request->per_page_record))
            {
                $perPage = $request->per_page_record;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

                $pagination =  [
                    'data' => $result,
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => ceil($total / $perPage)
                ];
                $query = $pagination;
            }
            else
            {
                $query = $query->get();
            }

            return response(prepareResult(true, $query, trans('translate.fetched_records')), 200 , ['Result'=>'Your data has been saved successfully']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'table_number'                    => 'required|numeric',
            // 'cartTotalQuantity'                => 'required|numeric',
            'order_status'                   => 'nullable|numeric',
            // 'cartTotalAmount'                => 'required|numeric',
            'taxes'                      => 'nullable|numeric',
            // 'netAmount'                      => 'nullable|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }
        DB::beginTransaction();
        try {
            $info = new Order;
            $info->table_number = $request->table_number;
            $info->cartTotalQuantity = $request->cartTotalQuantity;
            $info->cartTotalAmount = $request->cartTotalAmount;
            $info->taxes = $request->taxes;
            $info->netAmount = $request->netAmount;
            $info->order_status = $request->order_status;
            $info->save();

            foreach ($request->order_contains as $key => $order) {

                // search query for data from another table
                $productMenuItem = ProductMenu::find( $order['product_menu_id']);
                
                $addorder = new OrderContain;
                $addorder->order_id =  $info->id;
                $addorder->product_menu_id = $order['product_menu_id'];
                $addorder->category_id = $order['category_id'];
                $addorder->order_duration = $order['order_duration'];
                $addorder->instructions = $order['instructions'] ?? "";

                // below data is from another table
                // $addorder->name = $productMenuItem->name;
                $addorder->name = $order['name'];
                $addorder->quantity = $order['quantity'];

                 // below data is from another table
                // $addorder->price = $productMenuItem->price;
                $addorder->price = $order['price'];
                // $addorder->netPrice = $order['quantity'] * $productMenuItem->price ;
                $addorder->netPrice = $order['netPrice'];
                $addorder->save();
                
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
             $info['order_contains'] = $info->orderContains;
            return response()->json(prepareResult(true, $info, trans('translate.created')), 200 , ['Result'=>'Your data has been saved successfully']);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }
    
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'table_number'                    => 'required|numeric',
            // 'cartTotalQuantity'                => 'required|numeric',
            'order_status'                   => 'nullable|numeric',
            // 'cartTotalAmount'                => 'required|numeric',
            'taxes'                      => 'nullable|numeric',
            // 'netAmount'                      => 'nullable|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {
            $info = Order::find($id);
            $info->table_number = $request->table_number;
            $info->cartTotalQuantity = $request->cartTotalQuantity;
            $info->cartTotalAmount = $request->cartTotalAmount;
            $info->taxes = $request->taxes;
            $info->netAmount = $request->netAmount;
            $info->order_status = $request->order_status;
            $info->save();

           $deletOld = OrderContain::where('order_id', $id)->delete();
           
            foreach ($request->order_contains as $key => $order) {
               // search query for data from another table
               $productMenuItem = ProductMenu::find( $order['product_menu_id']);
                
               $addorder = new OrderContain;
               $addorder->order_id =  $info->id;
               $addorder->product_menu_id = $order['product_menu_id'];
               $addorder->category_id = $order['category_id'];
               $addorder->order_duration = $order['order_duration'];
               $addorder->instructions = $order['instructions'] ?? "";

               // below data is from another table
               // $addorder->name = $productMenuItem->name;
               $addorder->name = $order['name'];
               $addorder->quantity = $order['quantity'];

                // below data is from another table
               // $addorder->price = $productMenuItem->price;
               $addorder->price = $order['price'];
               // $addorder->netPrice = $order['quantity'] * $productMenuItem->price ;
               $addorder->netPrice = $order['netPrice'];
               $addorder->save();
                
            }
 
            //     // database sum querry
            // $quantitySum= DB::table('order_contains')->where('order_contains.order_id', $info->id)->sum('quantity');
            // $amountSum= DB::table('order_contains')->where('order_contains.order_id', $info->id)->sum('netPrice');

            // $info = Order::find( $info->id);
            // $info->cartTotalQuantity = $quantitySum;
            // $info->cartTotalAmount = $amountSum;
            // $info->taxes = $request->taxes;
            // $info->netAmount = $amountSum + $request->taxes;
            // $info->save();
            DB::commit();
            $info['order_contains'] = $info->orderContains;
            return response()->json(prepareResult(true, $info, trans('translate.created')), 200 , ['Result'=>'Your data has been saved successfully']);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function show($id)
    {
        try {
            
            $info = Order::find($id);
            if($info)
            {
                // return response(prepareResult(false, $info, trans('translate.fetched_records')), config('httpcodes.success'));
                return response(prepareResult(true, $info, trans('translate.fetched_records')), 200 , ['Result'=>'httpcodes.found']);
            }
            return response(prepareResult(false, [], trans('translate.record_not_found')),500,  ['Result'=>'httpcodes.not_found']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'httpcodes.internal_server_error']);
        }
    }

    public function destroy($id)
    {
        try {
            
            $info = Order::find($id);
            if($info)
            {
                $result=$info->delete();
                return response(prepareResult(true, $result, trans('sucess')), 200 , ['Result'=>'httpcodes.found']);
            }
            return response(prepareResult(false, [], trans('translate.record_not_found')),500,  ['Result'=>'httpcodes.not_found']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'httpcodes.internal_server_error']);
        }
    }
}
