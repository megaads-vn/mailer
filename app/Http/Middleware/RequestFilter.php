<?php 
namespace App\Http\Middleware;

use App\Http\Controllers\EmailService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
        $this->emailNotify($clientSource, $cacheKey, $request);
        $isBlockRequest = false;
        $count = 1;
        if (Cache::has($cacheKey)) {
            $count += Cache::get($cacheKey);
            if ($count > $requestPerDay) {
                $isBlockRequest = true;
                $message = "Daily quota exceeded.";
                Log::error('SEND_EMAIL_QUOTA_EXCEEDED: ' . json_encode($request->all()));
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
        $configSources = config('request.source_quota');
        $retval = $configSources['default'];
        if ($source != '' && array_key_exists($source, $configSources)) {
            $retval = $configSources[$source];            
        }
        return $retval;
    }

    private function emailNotify($source, $cacheKey) {
        $source = ($source !== '') ? $source : 'default';
        $configSource = config('request.source_quota');
        $notifyTo = config('request.notify_group');
        $requestTimes = 0;
        if (array_key_exists($source, $configSource)) {
            $configQuota = $configSource[$source];
        }
        if (Cache::has($cacheKey)) {
            $requestTimes = Cache::get($cacheKey);
        }
        if ($requestTimes == $configQuota) {
            $email = new EmailService();
            Cache::increment($cacheKey, 1);
            $content = '<h4>Hi Admin, </h4>';
            $content .= '<p>Request send email from source <strong>' . ucfirst($source) . '</strong>  reached their limit. Please check again!</p>';
            $content .= '<p>Best Regard,</p>';
            $request = new Request([
                'to' => $notifyTo,
                'subject' => 'Mailler[' . ucfirst($source) . ']: Daily quota exceeded.',
                'content' => $content
            ]);
            $email->notifyEmail($request);
        }
    }
}