<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");
        $auteticate = true;

        if(!$token){
            $auteticate = false;
        }
         $user = User::where("token", $token)->first();

        if(!$user){
            $auteticate = false;
        }else{
            Auth::login($user);
        }

        // Auth::login($user);
        if($auteticate){
            return $next($request);
        };

        return response([
            "message"=> "unauthorize",
        ],401);
    }
}
