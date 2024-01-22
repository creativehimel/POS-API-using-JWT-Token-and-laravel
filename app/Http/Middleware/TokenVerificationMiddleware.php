<?php

namespace App\Http\Middleware;

use App\Helper\JWTToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("token");
        $result = JWTToken::verifiyToken($token);
        if ($result == 'unauthorized') {
            return response()->json([
                'status'=> 401,
                'message'=> 'Unauthorized User.',
            ], 401);
        }else{
            $request->headers->set('email', $result->email);
            $request->headers->set('id', $result->id);
            return $next($request);
        }

    }
}
