<?php 
namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Cache;

class RequestFilter 
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $clientIp = $request->ip();
        $message = "";
        $cacheKey =  'request::' . $clientIp;
        $headerBlocked = $this->blockByHeader($request);
        $isBlockRequest = false;
        if (Cache::has($cacheKey)) {
            $isBlockRequest = true;
            $message = "Too many request in a minute";
        } else if ($headerBlocked && Cache::has($cacheKey)) {
            $cacheKey .= '::increment';
            $count = 1;
            if (Cache::has($cacheKey)) {
                $count = Cache::get($cacheKey);
                if ($count > 10) {
                    $isBlockRequest = true;
                    $message = "Daily quotar exceeded.";
                } else {
                    Cache::increment($cacheKey, 1);
                }
            } else {
                $expiredAt = Carbon::now()->addHours(24);
                Cache::put($cacheKey, $count, $expiredAt);
            }
        } else {
            $requestExpired = Carbon::now()->addMinutes(1);
            Cache::put($cacheKey, true, $requestExpired);
        }
        if ($isBlockRequest) {
            return response()->json(['status' => 'fail', 'message' => $message], 403);
        }
        return $next($request);
    }

    private function blockByHeader($request) {
        $retval = false;
        $header = $request->header('user-agent');
        preg_match('/(Chiaki\/1\s+CFNetwork|okhttp)/i', $header, $matches);
        if (count($matches) > 0) {
            $retval = true;            
        }
        return $retval;
    }
}