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
        $clientSource = $request->get('source', '');
        $message = "";
        $cacheKey =  'request::' . $clientSource . '::increment';
        $requestPerDay = $this->getRequestPerDayBySource($clientSource);
        $isBlockRequest = false;
        $count = 1;
        if (Cache::has($cacheKey)) {
            $count += Cache::get($cacheKey);
            if ($count > $requestPerDay) {
                $isBlockRequest = true;
                $message = "Daily quota exceeded.";
            } else {
                Cache::increment($cacheKey, 1);
            }
        } else {
            $expiredAt = Carbon::now()->addHours(24);
            Cache::put($cacheKey, $count, $expiredAt);
        }
        if ($isBlockRequest) {
            return response()->json(['status' => 'fail', 'message' => $message], 403);
        }
        return $next($request);
    }

    private function getRequestPerDayBySource($source) {
        $retval = 0;
        $configSources = config('request');
        $retval = $configSources['default'];
        if ($source != '' && array_key_exists($source, $configSources)) {
            $retval = $configSources[$source];            
        }
        return $retval;
    }
}