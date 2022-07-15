<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductStockManage;
use App\Models\ProductMenu;
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
            ->select('product_stock_manages.*', 'product_infos.name as product_infos_name', 'units.name as units_name' ,'product_infos.current_quanitity as product_infos_old_quantity',)
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
            'stock_operation'                => 'required',
            // 'stock_operation'                => [new Enum(ServerStatus::class)],
            'product_id'                   => 'required|numeric',
            // 'old_stock'                => 'nullable|numeric',
            // 'new_stock'                => 'nullable|numeric',
            'change_stock'                      => 'required|numeric',
            'unit_id'                      => 'required|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
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
            ? $old->current_quanitity + $request->change_stock 
            : $old->current_quanitity - $request->change_stock;

            $info->stock_operation = $request->stock_operation;
            $info->save();

            // updating the productinfo table as well
            $updateStock = ProductInfo::find( $request->product_id);
            $updateStock->current_quanitity = $info->new_stock;
            $updateStock->save();

            DB::commit();
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
            'stock_operation'                => 'required',
            'product_id'                   => 'required|numeric',
            'old_stock'                => 'nullable|numeric',
            'new_stock'                => 'nullable|numeric',
            'change_stock'                      => 'required|numeric',
            'unit_id'                      => 'required|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {

             // getting old stock value
             $old = ProductInfo::where('product_infos.id', $request->product_id)->get('current_quanitity')->first();
           

             $info = ProductStockManage::find($id);
             $info->product_id = $request->product_id;
             $info->unit_id = $request->unit_id;
 
             // storing old stock from product infos stock table
             $info->old_stock = $old->current_quanitity;
             $info->change_stock = $request->change_stock;
 
             // stock in/out calculation
             $info->new_stock = strtolower($request->stock_operation) == "in" 
             ? $old->current_quanitity + $request->change_stock 
             : $old->current_quanitity - $request->change_stock;
 
             $info->stock_operation = $request->stock_operation;
             $info->save();
 
             // updating the productinfo table as well
             $updateStock = ProductInfo::find( $request->product_id);
             $updateStock->current_quanitity = $info->new_stock;
             $updateStock->save();

            DB::commit();
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
            
            $info = ProductStockManage::find($id);
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
            
            $info = ProductStockManage::find($id);
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
