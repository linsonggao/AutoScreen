<?php

namespace Lsg\AutoScreen\Support;

use App\Http\Requests\Support\BaseRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class MakeValidateRequest extends BaseRequest
{
    /**
     * rules
     *
     * @return array
     */
    public function rules()
    {
        $nowActionKey = Route::currentRouteAction();
        if ($ruleConfig = Cache::get($nowActionKey . 'rule')) {
            return $ruleConfig;
        }
        list($ruleConfig, $attrConfig) = $this->makeValidateCache($nowActionKey);

        $ruleConfig = Cache::get($nowActionKey . 'rule') ?? [];

        return $ruleConfig;
    }

    /**
     * attributes
     *
     * @return array
     */
    public function attributes()
    {
        $nowActionKey = Route::currentRouteAction();
        $attrConfig = Cache::get($nowActionKey . 'attr') ?? [];
        if ($attrConfig = Cache::get($nowActionKey . 'attr')) {
            return $attrConfig;
        }
        list($ruleConfig, $attrConfig) = $this->makeValidateCache($nowActionKey);
        $attrConfig = Cache::get($nowActionKey . 'attr') ?? [];

        return $attrConfig;
    }

    //更新路由验证的缓存
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
