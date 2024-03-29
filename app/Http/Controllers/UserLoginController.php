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
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use App\Mail\SendResetPassworkLink;
use Illuminate\Support\Facades\Mail;
// use Mail;

class UserLoginController extends Controller
{
    public function login(Request $request)
    {
       


       
        try {
            $user = User::where('email',$request->email)->first();
            $employeeInfo = Employee::where('email',$request->email)->first();

            if (!empty($user)) {

                $userRoleID = User::where('email',$request->email)->get('role_id')->first();

                $validation = Validator::make($request->all(),  [
                   
                    'email'                      => 'required|email',
                    'password'                  => 'required',
                    // 'entry_mode'                  => ($request->entry_mode =="web-0.0.1" && $userRoleID->role_id =="1 ") || ($request->entry_mode =="mobile" && ($userRoleID->role_id =="1" ||$userRoleID->role_id =="2") )  ? 'required' : 'declined:false', 
        
                   
                ],
                [
                    'entry_mode.declined' => 'Login not allowed to this user'
                ]);
        
                if ($validation->fails()) {
                    return response(prepareResult(false, $validation->errors(), trans('validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
                }
                
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
                    $data['employeeInfo'] =  $employeeInfo;
                    // $token = $user->createToken('authToken')->accessToken;
                   
                    // $token = auth()->user()->createToken('authToken')->accessToken;

                    // $info = "Hello world";
                    // return "Hello world";
                   
                    return response(prepareResult(true, $data, trans('logged in successfully')), 200,  ['Result'=>'logged in successfully']);

                    } else {
                        // return response(prepareResult(false, [], trans('message_wrong_password')), 500,  ['Result'=>'message_wrong_password']);
                        return response(prepareResult(false, [], trans('wrong email or password')), 500,  ['Result'=>'wrong email or password']);
                } 
             } else {
                return response(prepareResult(false, [], trans('user not found')), 500,  ['Result'=>'user not found']);    
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
            return response(prepareResult(false, [], trans('user not found')), 500,  ['Result'=>'user not found']);    
        }
        if(Auth::check()) {
                $tokenId = $request->bearerToken();
                Auth::user()->token()->revoke();
                 
                
                // $tokenRepository = app(TokenRepository::class);
                // $refreshTokenRepository = app(RefreshTokenRepository::class);
                
                // // Revoke an access token...
                // $tokenRepository->revokeAccessToken($tokenId);
                
                // // Revoke all of the token's refresh tokens...
                // $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

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
    
    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                "email" => 'required|email',
            ]);
            if ($validator->fails()) {
                return response(prepareResult(false, $validator->errors(), trans('validation_failed')), 500,  ['Result'=>'Your data has not been saved']);
            }
            $user = User::where('email',$request->email)->first();
            if (!empty($user)) {
                        
                    $token = (env('APP_ENV','local') == 'local') ?'123456' : \Str::random(60);
                    // $passowrd_link = '<a href="'.route('password.reset',$token).'" style="color: #000;font-size: 18px;text-decoration: underline;font-family: "Roboto Condensed", sans-serif;" target="_blank">Reset your password </a>';
                    $passowrd_link = '<a href=" www.google.com" style="color: #000;font-size: 18px;text-decoration: underline;font-family: "Roboto Condensed", sans-serif;" target="_blank">Reset your password </a>';
                    $passMessage = 'This email is to confirm a recent password reset request for your account. To confirm this request and reset your password Please click below link ';

                    User::updateOrCreate(['email'=>$user->email],['password_token'=>$token]);   
                    
                    $content = ([
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $token,
                    'passowrd_link' => $passowrd_link,
                    'passMessage' => $passMessage,
                    ]);  
                    $info= Mail::to($request->email)->send(new SendResetPassworkLink($content));       
                    // if(env('IS_MAIL_ENABLE',false) == true){   
                    //     Mail::to($request->email)->send(new SendResetPassworkLink($content));
                    // }
                    return response(prepareResult(true, $info, trans('password reset link')), 200,  ['Result'=>' successfully']);

            }else{
                return response(prepareResult(true, [], trans('user not found')), 500,  ['Result'=>' successfully']);
            }
        }
        catch (\Throwable $e) {
            Log::error($e);
            return response()->json(prepareResult(false, $e->getMessage(), trans('Error while featching Records')), 500,  ['Result'=>'Your data has not been saved']);
            
        }
    }


}
