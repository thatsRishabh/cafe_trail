<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class EmployeeController extends Controller
{
    public function searchEmployee(Request $request)
    {
        try {
            $query = Employee::select('*')
                    ->orderBy('id', 'desc');
            if(!empty($request->id))
            {
                $query->where('id', $request->id);
            }
            if(!empty($request->name))
            {
                $query->where('name', $request->name);
            }
            if(!empty($request->designation))
            {
                $query->where('designation', 'LIKE', '%'.$request->designation.'%');
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

            return response(prepareResult(true, $query, trans('Record Fatched Successfully')), 200 , ['Result'=>'Your data has been saved successfully']);
        } 
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('Error while fatching Records')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }
    
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),  [
            'name'                       => 'required',
            'designation'                => 'required',
            'email'                      => 'required|email|unique:App\Models\Employee,email',
            'birth_date'                  => 'required',
            'joining_date'                => 'required',
            'address'                     => 'required',
            'gender'                      => 'required',
            'mobile'                      => 'required|numeric|digits_between:10,10',
            'salary'                      => 'required|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {
            
            $infoUser = new User;
            $infoUser->name = $request->name;
            $infoUser->email = $request->email;
            $infoUser->password = Hash::make($request->password);
         
            $infoUser->save();


            $info = new Employee;
            $info->user_id = $infoUser->id;
            $info->name = $request->name;
            $info->designation = $request->designation;
            $info->email = $request->email;
            // $info->password = Hash::make($request->password);
            $info->birth_date = $request->birth_date;
            $info->joining_date = $request->joining_date;
            $info->address = $request->address;
            $info->gender = $request->gender;
            $info->mobile = $request->mobile;
            $info->salary = $request->salary;
            if(!empty($request->image))
            {
              $file=$request->image;
            $filename=time().'.'.$file->getClientOriginalExtension();
            $info->image=imageBaseURL().$request->image->move('assets',$filename);
            }

            $info->save();

            // $infoUser = new User;
            // $infoUser->name = $request->name;
            // $infoUser->email = $request->email;
            // $infoUser->password = Hash::make($request->password);
            // $infoUser->save();

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
        $emailCheck = Employee::where('id',  $id)->get('email')->first();

        $validation = Validator::make($request->all(), [
            'name'                       => 'required',
            'designation'                => 'required',
            // 'email'                      => 'required|email|unique:App\Models\Employee,email',
            'email'                      => $emailCheck->email == $request->email ? 'required' : 'required|email|unique:App\Models\Employee,email',
            'birth_date'                       => 'required',
            'joining_date'                => 'required',
            'address'                      => 'required',
            'gender'                       => 'required',
            'mobile'                      => 'required|numeric|digits_between:10,10',
            'salary'                      => 'required|numeric',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {
            $info = Employee::find($id);
            $info->name = $request->name;
            $info->designation = $request->designation;
            $info->email = $request->email;
            // $info->password = Hash::make($request->password);
            $info->birth_date = $request->birth_date;
            $info->joining_date = $request->joining_date;
            $info->address = $request->address;
            $info->gender = $request->gender;
            $info->mobile = $request->mobile;
            $info->salary = $request->salary;
            $info->save();

            $infoUser = User::find($info->user_id);
            $infoUser->name = $request->name;
            $infoUser->email = $request->email;
            $infoUser->password = Hash::make($request->password);
            $infoUser->save();

            DB::commit();
            return response()->json(prepareResult(true, $info, trans('Your data has been Updated successfully')), 200 , ['Result'=>'Your data has been saved successfully']);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollback();
            return response()->json(prepareResult(false, $e->getMessage(), trans('Your data has not been Updated')), 500,  ['Result'=>'Your data has not been saved']);
        }
    }

    public function show($id)
    {
        try {
            
            $info = Employee::find($id);
            if($info)
            {
                // return response(prepareResult(false, $info, trans('translate.fetched_records')), config('httpcodes.success'));
                return response(prepareResult(true, $info, trans('Record Fatched Successfully')), 200 , ['Result'=>'httpcodes.found']);
            }
            return response(prepareResult(false, [], trans('Error while fatching Records')),500,  ['Result'=>'httpcodes.not_found']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'httpcodes.internal_server_error']);
        }
    }

    public function destroy($id)
    {
        try {
            
            $info = Employee::find($id);
            if($info)
            {
                // $result=$info->delete();
                $infoUser = User::find($info->user_id);
                $resultUser=$infoUser->delete();
                return response(prepareResult(true, $resultUser, trans('Record Id Deleted Successfully')), 200 , ['Result'=>'httpcodes.found']);
            }
            return response(prepareResult(false, [], trans('Record Id Not Found')),500,  ['Result'=>'httpcodes.not_found']);
        } catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('translate.something_went_wrong')), 500,  ['Result'=>'httpcodes.internal_server_error']);
        }
    }
}
