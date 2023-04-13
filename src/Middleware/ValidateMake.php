<?php

namespace Lsg\AutoScreen\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class ValidateMake
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
    public function handle($request, Closure $next)
    {
        $nowActionKey = Route::currentRouteAction();
        if ($ruleConfig = Cache::get($nowActionKey . 'rule') && $attrConfig = Cache::get($nowActionKey . 'attr')) {
            $response = $next($request);

            return $response;
        }
        list($ruleConfig, $attrConfig) = $this->makeValidateCache($nowActionKey);
        $response = $next($request);

        return $response;
    }

    //更新路由的缓存
    public function makeValidateCache($nowActionKey)
    {
        $ruleConfigs = config('makeValidate');
        $ruleConfig = [];
        $attrConfig = [];
        foreach ($ruleConfigs as $param => $rule) {
            if (in_array($nowActionKey, $rule[0])) {
                $ruleConfig[$param] = $rule[1];
                $attrConfig[$param] = $rule[2];
            }
        }
        Cache::set($nowActionKey . 'rule', $ruleConfig);
        Cache::set($nowActionKey . 'attr', $attrConfig);

        return [$ruleConfig, $attrConfig];
    }
}
