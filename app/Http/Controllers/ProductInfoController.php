<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductInfo;
use App\Models\Unit;


class ProductInfoController extends Controller
{
    public function searchProductInfo(Request $request)
    {
    try {

            $query = DB::table('product_infos')
            ->join('units', 'product_infos.unit_id', '=', 'units.id')
            ->select('product_infos.*', 'units.name as units_name')
            ->orderBy('product_infos.id', 'desc');
          // in above we have to specify that it has to sort according to ID of which table, product_infos or Unit

            if(!empty($request->id))
            {
                $query->where('product_infos.id',  $request->id);
                // in above we are specifying that it has to match from Product_infos id
            }
            if(!empty($request->name))
            {
                $query->where('product_infos.name', $request->name);
            }
            if(!empty($request->description))
            {
                $query->where('description', 'LIKE', '%'.$request->description.'%');
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
            'name'                       => 'required',
            'description'                => 'required',
            'unit_id'                    => 'required|numeric',
            'quanitity'                  => 'required',
            'price'                      => 'required|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }
    
        DB::beginTransaction();
        try {
            $info = new ProductInfo;
            $info->name = $request->name;
            $info->description = $request->description;
            $info->unit_id = $request->unit_id;
            $info->quanitity = $request->quanitity;
            $info->price = $request->price;
            $info->save();
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
            'name'                       => 'required',
            'description'                => 'required',
            'unit_id'                    => 'required|numeric',
            'quanitity'                  => 'required',
            'price'                      => 'required|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }
    
        DB::beginTransaction();
        try {
            $info = ProductInfo::find($id);
            $info->name = $request->name;
            $info->description = $request->description;
            $info->unit_id = $request->unit_id;
            $info->quanitity = $request->quanitity;
            $info->price = $request->price;
            $info->save();
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
            
            $info = ProductInfo::find($id);
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
            
            $info = ProductInfo::find($id);
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

    // public function joinData()
    // {
    //     $info = DB::table('product_infos')
    //     ->join('units', 'product_infos.unit_id', '=', 'units.id')
    //     // ->join('orders', 'users.id', '=', 'orders.user_id')
    //     // ->select('units.*','units.id as units_id', 'units.name as units_name ') 
    //     ->select('product_infos.*','units.id as units_id', 'units.name as units_name ') 
    //     ->orderBy('id', 'desc')
    //     ->get();

    //     return  $info;
    //     // print_r($info->all());
    // }
    
}
