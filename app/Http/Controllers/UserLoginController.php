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

    public function changePassword(Request $request)
    {
        try {

            $user = auth()->user();
            $validation = Validator::make($request->all(),[
                'old_password'              => ['required'],
                'new_password'              => ['required', 'confirmed', 'min:6', 'max:25'],
                'new_password_confirmation' => ['required']
            ]);

            if ($validation->fails()) {
                return response(prepareResult(false, $validation->errors(), trans('validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
            }


            if(Hash::check($request->old_password, $user->password)) {
                $data['password'] =  \Hash::make($request->new_password);
                $updatePass = User::updateOrCreate(['id' => $user->id],$data);

                return response(prepareResult(true, [], trans('Password Updated Successfully')), 200,  ['Result'=>'logged out successfully']);
                
               
            }else{
                return response(prepareResult(false, [], trans('Incorrect old password, Please try again with correct password')), 500,  ['Result'=>'Incorrect old password, Please try again with correct password']);
               
            }
           
        } 
            catch (\Throwable $e) {
                Log::error($e);
                return response()->json(prepareResult(false, $e->getMessage(), trans('Error while featching Records')), 500,  ['Result'=>'Your data has not been saved']);
         }
    }
    

}
