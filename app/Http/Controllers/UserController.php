<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function userRegistration(Request $request)
    {
        try {
            // Request Validation
            $request->validate([
                'first_name' => 'required|string|min:3',
                'last_name' => 'required|string|min:3',
                'phone' => 'required|string|min:11',
                'email' => 'required|string|unique:users',
                'password' => 'required|string|min:6',
            ]);

            // User Create Process
            User::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'User registration sucessfully.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                $e->getMessage(),
            ], 200);
        }

    }

    public function userLogin(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|string',
                'password' => 'required|string|min:6',
            ]);

            $count = User::where('email', $request->email)
                ->where('password', $request->password)
                ->count();

            if ($count == 1) {
                $token = JWTToken::generateToken($request->email);

                return response()->json([
                    'status' => 'success',
                    'message' => 'User Login successfully',
                    'token' => $token,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized user.',
                ], 401);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function sentOTP(Request $request)
    {
        try {

            //Request field validate
            $request->validate([
                'email' => 'required|email'
            ]);
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
                    'message'=> 'Unauthorized user',
                    ], 200);
            }
        } catch (Exception $e) {}
    }
    public function verifyOTP(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string',
            ]);
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
        } catch (Exception $e) {
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage()
                ], 200);
        }

    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|confirmed'
            ]);
            $email = $request->header('email');
            $password = $request->password;
            User::where('email', $email)->update(['password'=> $password]);
            return response()->json([
                'status'=> 'success',
                'message'=> 'Password reset successfully'
                ],200);
        } catch (Exception $e) {
            return response()->json([
                'status'=> 'error',
                'message'=> "Something went wrong!"
                ], 200);
        }
    }
}
