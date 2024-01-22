<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function userRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'phone' => 'required|string|min:11',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'message'=> $validator->messages(),
                ],422);
        }else{
            // User Create Process
            $user = User::create($request->all());
            if($user){
                return response()->json([
                    'status'=> 'success',
                    'message'=> 'Registration successfully.'
                    ],200);
            }else{
                return response()->json([
                    'status'=> 500,
                    'message'=> 'Something went wrong!'
                    ],500);
            }
        }
    }

    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'message'=> $validator->messages(),
                ],422);
        }else{

            $count = User::where('email', $request->email)
                ->where('password', $request->password)
                ->select('id')->first();

            if ($count !== null) {
                $token = JWTToken::generateToken($request->email, $count->id);
                return response()->json([
                    'status' => 'success',
                    'message' => 'User Login successfully',
                    'token' => $token,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Invalid credentials. Please enter right email or password.',
                ], 200);
            }
        }
    }

//    public function userLogout(){
//        return redirect('/login')->cookie('token', '', -1);
//    }

    public function sentOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'message'=> $validator->messages(),
                ],422);
        }else{
            $email= $request->email;

            //Generate 6 digit OTP
            $otp = rand(100000, 900000);

            $count = User::where('email', $email)->count();
            if ($count == 1) {
                Mail::to($email)->send(new OTPMail($otp));
                User::where('email', $email)->update(['otp'=> $otp]);
                return response()->json([
                    'status'=> 'success',
                    'message'=> '6 digit OTP code has been sent successfully.'
                ],200);

            }else{
                return response()->json([
                    'status'=> 'error',
                    'message'=> 'User not found!!',
                    ], 200);
            }
        }
    }
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'message'=> $validator->messages(),
                ],422);
        }else{
            $email= $request->email;
            $otp = $request->otp;
            $count = User::where('email', $email)
                    ->where('otp', $otp)
                    ->count();
            if ($count == 1) {
                //Update OTP field in databas
                User::where('email', $email)->update(['otp'=> 0]);
                //Generate reset password token
                $token = JWTToken::generatePasswordResetToken($email);
                //
                return response()->json([
                    'status'=> 'success',
                    'message'=> 'OTP verification successfully.',
                    'token'=> $token
                ],200);

            }else{
                return response()->json([
                    'status'=> 'error',
                    'message'=> 'Invalid OTP',
                    ], 200);
            }
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed'
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'message'=> $validator->messages(),
                ],422);
        }else{
            $email = $request->header('email');
            $password = $request->password;
            $result = User::where('email', $email)->update(['password'=> $password]);
            if($result){
                return response()->json([
                    'status'=> 'success',
                    'message'=> 'Password reset successfully.'
                    ],200);
            }else{
                return response()->json([
                    'status'=> 'error',
                    'message'=> 'Something went wrong!'
                    ],500);
            }
        }
    }

    public function getUserProfile(Request $request){
        $email = $request->header('email');
        $userDetails = User::where('email', $email)->first();
        return response()->json([
            'status' => 'success',
            'data' => $userDetails
        ], 200);
    }

    public function updateUserProfile(Request $request){
        $email = $request->header('email');
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'phone' => 'required|string|min:11',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => 422,
                'message'=> $validator->messages(),
            ],422);
        }else{
            $user = User::where('email', $email)->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'password' => $request->password
            ]);

            if($user){
                return response()->json([
                    'status'=> 'success',
                    'message'=> 'User profile updated successfully.'
                ],200);
            }else{
                return response()->json([
                    'status'=> 500,
                    'message'=> 'Something went wrong!'
                ],500);
            }
        }
    }
}
