<?php 
namespace App\Http\Middleware;
use Closure;

class SsoAuthenticate 
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = $request->session()->get('user');
        if (!$user) {
            $request->session()->put('redirect_url', $request->fullUrl());
            $ssoUrl = 'https://id.megaads.vn/system/home/login?continue=http://mailer.megaads.net/sso/callback';
            return redirect()->to($ssoUrl);
        }
        // if ($request->getUser() != env('API_USERNAME') || $request->getPassword() != env('API_PASSWORD')) {
        //     $headers = array('WWW-Authenticate' => 'Basic');
        //     return response('Unauthorized', 401, $headers);
        // }
        return $next($request);
    }
}