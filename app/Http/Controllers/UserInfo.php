<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\UnitData;


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


    public function testKits(Request $request)
    {
        try {
            $query = UnitData::select('*');
            if(!empty($request->id))
            {
                $query->where('id', $request->id);
            }
            // if(!empty($request->test_kit_type))
            // {
            //     $query->where('test_kit_type', $request->test_kit_type);
            // }
            // if(!empty($request->commercial_name))
            // {
            //     $query->where('commercial_name', 'LIKE', '%'.$request->commercial_name.'%');
            // }
            // if(!empty($request->manufacturer_country))
            // {
            //     $query->where('manufacturer_country', 'LIKE', '%'.$request->manufacturer_country.'%');
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

            return response(prepareResult(false, $query, trans('translate.fetched_records')), 200 , ['Result'=>'Your data has been saved successfully']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(true, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'                    => 'required',
            'name'                  => 'required',
            'abbreiation'           => 'required',
            'minvalue'              => 'required',
            // 'test_kit_type'  => 'required|in:NAAT,RAT,OTHER',
        ]);

        if ($validation->fails()) {
            return response(prepareResult(true, $validation, trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
            // return response(prepareResult(true, $validation->getMessage(), trans('translate.validation_failed')),500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();

        try {
            $testsKit = new UnitData;
            $testsKit->id = $request->id;
            $testsKit->name = $request->name;
            $testsKit->abbreiation  = $request->abbreiation;
            $testsKit->minvalue  = $request->minvalue;
            // $testsKit->manufacturer_country  = $request->manufacturer_country;
            // $testsKit->manufacturer_website  = $request->manufacturer_website;
            // $testsKit->hsc_common_list  = empty($request->hsc_common_list) ? 0 : $request->hsc_common_list;
            // $testsKit->hsc_mutual_recognition  = empty($request->hsc_mutual_recognition) ? 0 : $request->hsc_mutual_recognition;
            // $testsKit->test_kit_type  = $request->test_kit_type;
            // $testsKit->status  = empty($request->status) ? 0 : $request->status;
            $testsKit->save();
            DB::commit();
            return response()->json(prepareResult(false, $testsKit, trans('translate.created')), 200 , ['Result'=>'Your data has been saved successfully']);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(prepareResult(true, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function show($id)
    {
        try {
            $testsKit = UnitData::find($id);
            if($testsKit)
            {
                return response(prepareResult(false, $testsKit, trans('translate.fetched_records')), config('httpcodes.success'));
            }
            return response(prepareResult(true, [], trans('translate.record_not_found')),500,  ['Result'=>'httpcodes.not_found']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(true, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'httpcodes.internal_server_error']);
        }
    }
}
