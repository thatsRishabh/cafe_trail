<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(),  [
           
            'email'                      => 'required|email',
            'password'                  => 'required',
           
        ]);

        if ($validation->fails()) {
            return response(prepareResult(false, $validation->errors(), trans('validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
        }

       
        try {
            $user = User::where('email',$request->email)->first();

            if (!empty($user)) {
                if (Hash::check($request->password, $user->password)) {

                    $data = [];

                    
                    $data['token'] = $user->createToken('authToken')->accessToken;
                    $data['email'] = $request->email;
                    $permissionData[] =[
                        'action'=>"dashboard",
                        'name'=>"dashboard-view",
                    ];
                    $data['permissions'] =  $permissionData;
                    $userData =[
                        'role'=>"admin"
                    ];
                    $data['user'] =  $userData;
                    // $token = $user->createToken('authToken')->accessToken;
                   
                    // $token = auth()->user()->createToken('authToken')->accessToken;

                    // $info = "Hello world";
                    // return "Hello world";
                   
                    return response(prepareResult(true, $data, trans('logged in successfully')), 200,  ['Result'=>'logged in successfully']);

                    } else {
                        return response(prepareResult(false, [], trans('message_wrong_password')), 500,  ['Result'=>'message_wrong_password']);
            } 
             } else {
                return response(prepareResult(false, [], trans('message_user_not_found')), 500,  ['Result'=>'message_user_not_found']);    
            }
            
         } catch (\Throwable $e) {
                Log::error($e);
                return response()->json(prepareResult(false, $e->getMessage(), trans('Error while featching Records')), 500,  ['Result'=>'Your data has not been saved']);
            }
   }

   public function logout(Request $request)
    {
        $user = getUser();
        if (!is_object($user)) {
            return response(prepareResult(false, [], trans('message_user_not_found')), 500,  ['Result'=>'message_user_not_found']);    
        }
        if(Auth::check()) {
            $token = $request->bearerToken();
            Auth::user()->token()->revoke();
            return response(prepareResult(true, [], trans('logged out successfully')), 200,  ['Result'=>'logged out successfully']);
        }else{
            return response(prepareResult(false, [], trans('internal_server_error')), 500,  ['Result'=>'internal_server_error']);    
        }
        // return  $request->bearerToken();
        return $user;

    }


//    public function login(Request $request)
//     {
//         $validator = Validator::make($request->all(),[
//             $this->username() => 'required|email',
//             'password' => 'required|string',
//             ],
//             [
//             'email.required' => getLangByLabelGroups('LoginValidation','message_email'),
//             'email.email' => getLangByLabelGroups('LoginValidation','message_email_invalid'),
//             'password.required' =>  getLangByLabelGroups('LoginValidation','message_password'),
//             ]);

//         if ($validator->fails()) {
//             return prepareResult(false,$validator->errors()->first(),[], config('httpcodes.bad_request')); 
//         }
//         try {
//             $user = User::where('email',$request->email)->with('TopMostParent:id,user_type_id,name,email')
//             ->withoutGlobalScope('top_most_parent_id')
//             ->first();
//             if (!empty($user)) {
                
//                 if (Hash::check($request->password, $user->password)) {
//                     if($user->status == '0' ) { 
//                         return prepareResult(false,getLangByLabelGroups('LoginValidation','message_account_inactive'),[],config('httpcodes.unauthorized'));
//                     }
//                     if($user->status == '2') { 
//                         return prepareResult(false,getLangByLabelGroups('LoginValidation','message_account_deactive'),[],config('httpcodes.unauthorized'));
//                     }
                    
//                     if ($this->attemptLogin($request)) {
//                             $token = auth()->user()->createToken('authToken')->accessToken;
//                             if (empty($token)) {
//                                 return prepareResult(false,getLangByLabelGroups('LoginValidation','message_unable_generate_token'),[], config('httpcodes.bad_request'));
//                             }else{
//                                  //======= login history==================//
//                                 $history =  DeviceLoginHistory::where('user_id',$user->id)
//                                 ->where('login_via', $request->login_via)
//                                 ->where('device_token', $request->device_token)
//                                 ->where('device_id', $request->device_id)
//                                 ->first();
//                                 if ($history) {
//                                     $history->user_token = $token;
//                                     $history->ip_address = request()->ip();
//                                     $history->save();
//                                 }
//                                 else
//                                 {
//                                     $createHistory = DeviceLoginHistory::create([
//                                         'user_id'=> Auth::id(),
//                                         'login_via'=> ($request->login_via) ? $request->login_via:'0',
//                                         'device_token'=> $request->device_token,
//                                         'device_id'=> $request->device_id,
//                                         'user_token'=> $token,
//                                         'ip_address'=> request()->ip(),
//                                     ]);
//                                 }

//                                 if(auth()->user()->top_most_parent_id!=1)
//                                 {
//                                     $checkLicence = User::find(auth()->user()->top_most_parent_id);
//                                     if($checkLicence->license_end_date<date('Y-m-d'))
//                                     {
//                                         $checkLicence->license_status = 0;
//                                         $checkLicence->save();
//                                     }
//                                 }
                        
//                                 $user = User::where('id',$user->id)->with('TopMostParent:id,user_type_id,name,email','language:id,title,value')->first();    
//                                 $user['access_token'] = $token;
//                                 $user['user_type']    = @Auth::user()->UserType->name;
//                                 $user['roles']    = @Auth::user()->roles[0]->name;
//                                 $user['role_name']    = @Auth::user()->roles[0]->se_name;
//                                 $role   = Role::where('name', $user['roles'])->first();
//                                 $user['permissions']  = $role->permissions()->select('id','name as action','group_name as subject','se_name')->get();
//                                 $user['licence_status'] = 1;

//                                 if(auth()->user()->top_most_parent_id!=1)
//                                 {
//                                     $user['licence_status'] = User::find(auth()->user()->top_most_parent_id)->license_status;
//                                     $assigned_module = User::find(auth()->user()->top_most_parent_id);
//                                     $user['assigned_module'] = $assigned_module->assignedModule()->select('id','user_id','module_id')->with('Module:id,name')->get();

//                                     $checkFileAccess = AdminFile::where('user_type_id', auth()->user()->user_type_id)
//                                         ->first();
//                                     if($checkFileAccess)
//                                     {
//                                         $checkLog = FileAccessLog::where('admin_file_id', $checkFileAccess->id)
//                                         ->where('user_id', auth()->id())
//                                         ->first();
//                                         if($checkLog)
//                                         {
//                                             $user['file_access'] = null;
//                                         }
//                                         else
//                                         {
//                                             $user['file_access'] = $checkFileAccess;
//                                         }
//                                     }
//                                     else
//                                     {
//                                         $user['file_access'] = null;
//                                     }
//                                 }
//                                 else
//                                 {
//                                     $user['file_access'] = null;
//                                 }
                                
//                              return prepareResult(true,"User Logged in successfully",$user,config('httpcodes.success'));
//                             }
                        
//                     }
                   
//                 } else {
//                     return prepareResult(false,getLangByLabelGroups('LoginValidation','message_wrong_password'),[],config('httpcodes.bad_request'));
//                 }   
//             } else {
//                 return prepareResult(false,getLangByLabelGroups('LoginValidation','message_user_not_found'),[],config('httpcodes.bad_request'));
//             }
//         }
//         catch(Exception $exception) {
//             \Log::error($exception);
//             return prepareResult(false, $exception->getMessage(),[], config('httpcodes.internal_server_error'));
//        }
//    }
}
