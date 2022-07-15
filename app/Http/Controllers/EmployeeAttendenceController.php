<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeAttendence;
use App\Models\AttendenceList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployeeAttendenceController extends Controller
{
    public function searchEmployeeAttendence(Request $request)
    {
        try {
            $query = EmployeeAttendence::select('*')
                    ->with('attendenceMethod')
                    ->orderBy('id', 'desc');

            if(!empty($request->id))
            {
                $query->where('id', $request->id);
            }
            if(!empty($request->date))
            {
                $query->where('date', $request->date);
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
            'date'                    => 'required',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }
        DB::beginTransaction();
        try {
          
            $info = new EmployeeAttendence;
            $info->date = $request->date;
            $info->save();
           
           foreach ($request->employee_attendence as $key => $attendence) {
               $addAttendence = new AttendenceList;
               $addAttendence->attendence_id =  $info->id;
               $addAttendence->employee_id = $attendence['employee_id'];
               $addAttendence->attendence = $attendence['attendence'];
               $addAttendence->save();     
           }

            DB::commit();
            $info['attendence_lists'] = $info->attendenceMethod;
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
            'date'                    => 'required',
              
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('translate.validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

        DB::beginTransaction();
        try {

             $info = EmployeeAttendence::find($id);
            $info->date = $request->date;
            $info->save();

            $deletOld = AttendenceList::where('attendence_id', $id)->delete();
            foreach ($request->employee_attendence as $key => $attendence) {
               $addAttendence = new AttendenceList;
               $addAttendence->attendence_id =  $id;
               $addAttendence->employee_id = $attendence['employee_id'];
               $addAttendence->attendence = $attendence['attendence'];
               $addAttendence->save();      
           }


            DB::commit();
            $info['attendence_lists'] = $info->attendenceMethod;
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
            
            $info = EmployeeAttendence::find($id);
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
            
            $info = EmployeeAttendence::find($id);
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
