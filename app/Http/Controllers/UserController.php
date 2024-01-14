<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

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
                ], 200);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 200);
        }
    }
}
