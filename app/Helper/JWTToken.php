<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{
    public static function generateToken($email, $id): string
    {
        $key = env('APP_KEY');
        $playload = [
            'iss' => 'pos-api-laravel',
            'iat' => time(),
            'exp' => time() + 60 * 60,
            'email' => $email,
            'id' => $id
        ];
        $token = JWT::encode($playload, $key, 'HS256');

        return $token;
    }

    public static function verifiyToken($token):string|object|array
    {
        if($token == null){
            return response()->json([
                'status'=> 401,
                'message'=> 'Unauthorized User.',
            ], 401);
        }else{
            $key = env('APP_KEY');
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            if (!$decode) {
                return 'unauthorized';
            }else{
                return $decode;
            }
        }
    }
    public static function generatePasswordResetToken($email):string
    {
        $key = env('APP_KEY');
        $playload = [
            'iss' => 'pos-api-laravel',
            'iat' => time(),
            'exp' => time() + 60 * 5,
            'email' => $email,
            'id' => '0'
            ];
        $token = JWT::encode($playload,$key,'HS256');
        return $token;
    }

}
