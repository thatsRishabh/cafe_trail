<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function searchCustomer(Request $request)
    {
        try {
            $query = Customer::select('*')
                ->orderBy('id', 'desc');
            if(!empty($request->id))
            {
                $query->where('id', $request->id);
            }
            if(!empty($request->name))
            {
                $query->where('name', $request->name);
            }
            if(!empty($request->mobile))
            {
                $query->where('mobile', $request->mobile);
            }
            if(!empty($request->email))
            {
                $query->where('email', $request->email);
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
            'name'                    => 'required',
            'address'                 => 'required',
            'gender'                   => 'required',
            'email'                    => 'required|email|unique:App\Models\Customer,email',
            'mobile'                   => 'required|numeric|digits_between:10,10',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {
            $info = new Customer;
            $info->name = $request->name;
            $info->address = $request->address;
            $info->gender = $request->gender;
            $info->email = $request->email;
            $info->mobile = $request->mobile;
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
            'name'                    => 'required',
            'address'                 => 'required',
            'gender'                   => 'required',
            // 'email'                      => 'required|email|unique:App\Models\Customer,email',
            'mobile'                      => 'required|numeric|digits_between:10,10',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {
            $info = Customer::find($id);
            $info->name = $request->name;
            $info->address = $request->address;
            $info->gender = $request->gender;
            $info->email = $request->email;
            $info->mobile = $request->mobile;
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
            
            $info = Customer::find($id);
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
            
            $info = Customer::find($id);
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
