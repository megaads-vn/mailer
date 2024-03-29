<?php
namespace App\Http\Middleware;


use App\Models\User;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class JwtMiddleware
{

    public function handle($request, \Closure $next, $guard = NULL)
    {
        $token = $request->get('token');
        $authType = $request->get('auth_type', 'jwt');

        if ( !$token ) {
            return response()->json([
                'status' => 'failed',
                'code' => 'not_provided_token',
                'error' => 'Token not provided'
            ], 401);
        }
        if ($authType !== 'basic') {
            try {
                $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            } catch (ExpiredException $ex) {
                return response()->json([
                    'status' => 'failed',
                    'code' => 'token_expired',
                    'error' => 'Provided token is expired',
                ], 400);
            } catch (\Exception $ex) {
                return response()->json([
                    'status' => 'failed',
                    'code' => 'error',
                    'error' => 'An error while decoding token.'
                ], 400);
            }
    
            $user = User::query()->find($credentials->sub);
        } else {
            $user = User::query()->where('token', $token)->first();
        }

        $request->auth = $user;

        return $next($request);
    }
}