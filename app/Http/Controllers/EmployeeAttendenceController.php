<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployeeAttendence;
use App\Models\Employee;
use App\Models\AttendenceList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\PDF;
// use PDF;


class EmployeeAttendenceController extends Controller
{
    public function searchEmployeeAttendence(Request $request)
    {
        try {
            $query = EmployeeAttendence::select('*')
                    ->with('attendenceMethod:attendence_id,employee_id,attendence')
                    ->orderBy('id', 'desc');

            if(!empty($request->id))
            {
                $query->where('id', $request->id);
            }
            if(!empty($request->date))
            {
                $query->where('date', $request->date);
            }
            if(!empty($request->employee_id))
            {
                $query->where('employee_id', $request->employee_id);
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

    public function dateWiseSearch(Request $request) {
        $info = AttendenceList::where('employee_id', $request->employee_id);
        if(!empty($request->from_date) && !empty($request->end_date))
        {
            $info->whereDate('created_at', '>=', $request->from_date)->whereDate('created_at', '<=', $request->end_date);
        }
        elseif(!empty($request->from_date) && empty($request->end_date))
        {
            $info->whereDate('created_at', '>=', $request->from_date);
        }
        elseif(empty($request->from_date) && !empty($request->end_date))
        {
            $info->whereDate('created_at', '<=', $request->end_date);
        }
        $result= $info->get();
        return $result;
    }

    public function monthlyAttendence(Request $request) {
       

        $data = [];

        // use of template literal while adding date
        $data['total_days_present'] = AttendenceList::where('employee_id', $request->employee_id)->where('attendence',2)->whereDate('created_at', '>=', $request->year_month.'-01' )->whereDate('created_at', '<=', $request->year_month.'-31')->count();

        $data['total_days_absent'] = AttendenceList::where('employee_id', $request->employee_id)->where('attendence',1)->whereDate('created_at', '>=', $request->year_month.'-01' )->whereDate('created_at', '<=', $request->year_month.'-31')->count();

        $data['days_in_month'] = cal_days_in_month(CAL_GREGORIAN, substr($request->year_month, 5,6), substr($request->year_month, 0,4));
        $data['year_month']=$request->year_month;
       $employeeData = Employee::where('id', $request->employee_id)->get('salary')->first();
        $data['employeeSalary'] = $employeeData->salary;
   
        return $data;

    }

}
