<?php

namespace Lsg\AutoScreen\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class CacheMake
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param null|mixed $minutes
     * @param null|mixed $cacheKey
     * @param null|mixed $cacheMoreKey
     * @return mixed
     */
    public function handle($request, Closure $next, $minutes = 0, $cacheMoreKey = null)
    {
        $cacheKey = $cacheMoreKey . Route::currentRouteAction() . json_encode(request()->all());
        if (Cache::has($cacheKey) && env('APP_ENV') !== 'local') {
            $data = Cache::get($cacheKey);

            return json_decode($data, true)['original'];
        } else {
            $response = $next($request);
            $cache_time = $minutes * 60;
            // 缓存用户数据
            Cache::put($cacheKey, json_encode($response), $cache_time ?: env('CACHE_TIME', 600));

            return $response;
        }
    }
}
