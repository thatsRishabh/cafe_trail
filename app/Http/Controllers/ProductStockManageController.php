<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductStockManage;
use App\Models\ProductMenu;
use App\Models\Unit;
use App\Models\ProductInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Enums\ServerStatus;
use Illuminate\Validation\Rules\Enum;

class ProductStockManageController extends Controller
{
    public function searchProductStockManage(Request $request)
    {
        try {

            $query = DB::table('product_stock_manages')
            ->join('units', 'product_stock_manages.unit_id', '=', 'units.id')
            ->join('product_infos', 'product_stock_manages.product_id', '=', 'product_infos.id')
            ->select('product_stock_manages.*', 'product_infos.name as product_infos_name', 'units.name as units_name' ,'product_infos.current_quanitity as product_infos_old_quantity', 'units.minvalue as units_minvalue')
            ->orderBy('product_stock_manages.id', 'desc');

            if(!empty($request->id))
            {
                $query->where('product_stock_manages.id', $request->id);
            }
            if(!empty($request->product_id))
            {
                $query->where('product_stock_manages.product_id', $request->product_id);
            }
            if(!empty($request->stock_operation))
            {
                $query->where('product_stock_manages.stock_operation', $request->stock_operation);
            }
            if(!empty($request->product))
            {
                $query->where('product', 'LIKE', '%'.$request->product.'%');
            }

             // date wise filter from here
             if(!empty($request->from_date) && !empty($request->end_date))
            {
                $query->whereDate('product_stock_manages.created_at', '>=', $request->from_date)->whereDate('product_stock_manages.created_at', '<=', $request->end_date);
            }

            if(!empty($request->from_date) && !empty($request->end_date) && !empty($request->product_id))
            {
                $query->where('product_id', $request->product_id)->whereDate('product_stock_manages.created_at', '>=', $request->from_date)->whereDate('product_stock_manages.created_at', '<=', $request->end_date);
            }

            // elseif(!empty($request->from_date) && empty($request->end_date))
            // {
            //     $query->where('customer_id', $request->customer_id)->whereDate('product_stock_manages.created_at', '>=', $request->from_date);
            // }
            // elseif(empty($request->from_date) && !empty($request->end_date))
            // {
            //     $query->where('customer_id', $request->customer_id)->whereDate('product_stock_manages.created_at', '<=', $request->end_date);
            // }

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

            return response(prepareResult(true, $query, trans('Record Fatched Successfully')), 200 , ['Result'=>'Your data has been saved successfully']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('Error while fatching Records')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }
    
    public function store(Request $request)
    {
        // $temp= unitSimilarTypeCheck($request->unit_id,$request->product_id);
        // return $temp;
        $old = ProductInfo::where('product_infos.id', $request->product_id)->get('current_quanitity')->first();

        $validation = Validator::make($request->all(), [
            'stock_operation'                => 'required',
            // 'stock_operation'                => [new Enum(ServerStatus::class)],
            'product_id'                   => 'required|numeric',
            // 'old_stock'                => 'nullable|numeric',
            // 'new_stock'                => 'nullable|numeric',
            // 'change_stock'                      => 'required|integer|gte:10',
            'change_stock'                      => (strtolower($request->stock_operation) == "out") 
            && ($old->current_quanitity) < unitConversion($request->unit_id, ($request->change_stock))
            ? 'required|declined:false' : 'required|gte:1',
            // 'unit_id'                      => 'required|numeric',
            'unit_id'                      => unitSimilarTypeCheck($request->unit_id,$request->product_id),
           
        ],
        [
            'change_stock.declined' => 'Low quantity in stock',
            'unit_id.declined' => 'Invalid Unit Type'
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }
        DB::beginTransaction();
        try {
            // getting old stock value
            $old = ProductInfo::where('product_infos.id', $request->product_id)->get('current_quanitity')->first();
            $info = new ProductStockManage;
            $info->product_id = $request->product_id;
            $info->unit_id = $request->unit_id;

            // storing old stock from product infos stock table
            $info->old_stock = $old->current_quanitity;
            $info->change_stock = $request->change_stock;

            // stock in/out calculation
            $info->new_stock = strtolower($request->stock_operation) == "in" 
            ? $old->current_quanitity + unitConversion($request->unit_id, $request->change_stock) 
            : $old->current_quanitity - unitConversion($request->unit_id, $request->change_stock);

            $info->stock_operation = $request->stock_operation;
            $info->save();

            // updating the productinfo table as well
            $updateStock = ProductInfo::find( $request->product_id);
            $updateStock->current_quanitity = $info->new_stock;
            $updateStock->save();

            DB::commit();
            return response()->json(prepareResult(true, $info, trans('Your data has been saved successfully')), 200 , ['Result'=>'Your data has been saved successfully']);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(prepareResult(false, $e->getMessage(), trans('Your data has not been saved')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function update(Request $request, $id)
    {
       
        $validation = Validator::make($request->all(), [
            'stock_operation'          => 'required',
            'product_id'               => 'required|numeric',
            'old_stock'                => 'nullable|numeric',
            'new_stock'                => 'nullable|numeric',
            // 'change_stock'                      => (strtolower($request->stock_operation) == "out") 
            // && (($old->current_quanitity) < unitConversion($request->unit_id, ($request->change_stock)))
            // ? 'required|declined:false' : 'required|gte:1',
            // 'unit_id'                  => 'required|numeric',
            'unit_id'                      => unitSimilarTypeCheck($request->unit_id,$request->product_id),
           
        ],
        [
            // 'change_stock.declined' => 'low quantity in stock',
            'unit_id.declined' => 'Invalid Unit Type',

        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

            $oldStockValue =ProductStockManage::find($id);
            $unitData =Unit::find($oldStockValue->unit_id);

        //   // restoring productinfo old stock to previous value after kg/gram/dozen conversion
        //   $updateStock = ProductInfo::find( $request->product_id);
        //   $updateStock->current_quanitity = strtolower($oldStockValue->stock_operation) == "in" 
        //   ? $oldStockValue->new_stock - ($oldStockValue->change_stock * $unitData->minvalue)  
        //   : $oldStockValue->new_stock + ($oldStockValue->change_stock * $unitData->minvalue);
        //   $updateStock->save();

        $updateStock = ProductInfo::find( $request->product_id);
        $checkQuanitity = strtolower($oldStockValue->stock_operation) == "in" 
        ? $oldStockValue->new_stock - ($oldStockValue->change_stock * $unitData->minvalue)  
        : $oldStockValue->new_stock + ($oldStockValue->change_stock * $unitData->minvalue);
        // $updateStock->save();

        // $old = ProductInfo::where('product_infos.id', $request->product_id)->get('current_quanitity')->first();

        $validator = Validator::make($request->all(), [
            'change_stock'                      => (strtolower($request->stock_operation) == "out") 
            && (($checkQuanitity) < unitConversion($request->unit_id, ($request->change_stock)))
            ? 'required|declined:false' : 'required|gte:1',
           
        ],
        [
            'change_stock.declined' => 'low quantity in stock',

        ]);

        if ($validator->fails()) {
            return response(prepareResult(false, $validator->errors(), trans('validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {

            $oldStockValue =ProductStockManage::find($id);
            $unitData =Unit::find($oldStockValue->unit_id);

            
              // restoring productinfo old stock to previous value after kg/gram/dozen conversion
              $updateStock = ProductInfo::find( $request->product_id);
              $updateStock->current_quanitity = strtolower($oldStockValue->stock_operation) == "in" 
              ? $oldStockValue->new_stock - ($oldStockValue->change_stock * $unitData->minvalue)  
              : $oldStockValue->new_stock + ($oldStockValue->change_stock * $unitData->minvalue);
              $updateStock->save();

            //  getting old stock value
             $old = ProductInfo::where('product_infos.id', $request->product_id)->get('current_quanitity')->first();
           
             $info = ProductStockManage::find($id);
             $info->product_id = $request->product_id;
             $info->unit_id = $request->unit_id;
 
             // storing old stock from product infos stock table
             $info->old_stock = $old->current_quanitity;
             $info->change_stock = $request->change_stock;
 
              // stock in/out calculation
            $info->new_stock = strtolower($request->stock_operation) == "in" 
            ? $old->current_quanitity + unitConversion($request->unit_id, $request->change_stock) 
            : $old->current_quanitity - unitConversion($request->unit_id, $request->change_stock);
            // : $old->current_quanitity = - unitConversion($request->unit_id, $request->change_stock);

            $info->stock_operation = $request->stock_operation;
            $info->save();

             // updating the productinfo table as well
             $updateStock = ProductInfo::find( $request->product_id);
             $updateStock->current_quanitity = $info->new_stock;
             $updateStock->save();

            DB::commit();
            return response()->json(prepareResult(true,  $info, trans('Your data has been Updated successfully')), 200 , ['Result'=>'Your data has been saved successfully']);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(prepareResult(false, $e->getMessage(), trans('Your data has not been Updated')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function show($id)
    {
        try {
            
            $info = ProductStockManage::find($id);
            if($info)
            {
                // return response(prepareResult(false, $info, trans('Record Fatched Successfully')), config('httpcodes.success'));
                return response(prepareResult(true, $info, trans('Record Fatched Successfully')), 200 , ['Result'=>'httpcodes.found']);
            }
            return response(prepareResult(false, [], trans('Error while featching Records')),500,  ['Result'=>'httpcodes.not_found']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('Internal Server Error')), 500,  ['Result'=>'httpcodes.internal_server_error']);
        }
    }

    public function destroy($id)
    {
        try {
            
            $info = ProductStockManage::find($id);
            if($info)
            {
                $result=$info->delete();
                return response(prepareResult(true, $result, trans('Record Id Deleted Successfully')), 200 , ['Result'=>'httpcodes.found']);
            }
            return response(prepareResult(false, [], trans('Record Id Not Found')),500,  ['Result'=>'httpcodes.not_found']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('Internal Server Error')), 500,  ['Result'=>'httpcodes.internal_server_error']);
        }
    }

}
