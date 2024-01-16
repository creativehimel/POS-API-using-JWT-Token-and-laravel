<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{
    public static function generateToken($userEmail): string
    {
        $key = env('APP_KEY');
        $playload = [
            'iss' => 'pos-api-laravel',
            'iat' => time(),
            'exp' => time() + 60 * 60,
            'email' => $userEmail,
        ];
        $token = JWT::encode($playload, $key, 'HS256');

        return $token;
    }

    public static function verificToken($token)
    {
        try {
            $key = env('APP_KEY');
            $decode = JWT::decode($token, new Key($key, 'HS256'));

            return $decode->email;
        } catch (Exception $e) {
            return 'unauthorized';
        }

    }
    public static function generatePasswordResetToken($email){
        $key = env('APP_KEY');
        $playload = [
            'iss'=> 'pos-api-laravel',
            'iat' => time(),
            'exp'=> time() + 60*5,
            'email'=> $email,
            ];
        $token = JWT::encode($playload,$key,'HS256');
        return $token;
    }
    public static function verifyResetToken($token){
        $key = env('APP_KEY');
    }
}
