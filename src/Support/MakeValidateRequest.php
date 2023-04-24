<?php

namespace Lsg\AutoScreen\Support;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class MakeValidateRequest extends FormRequest
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
        list($ruleConfig, $attrConfig, $messageConfig) = $this->makeValidateCache($nowActionKey);

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
        list($ruleConfig, $attrConfig, $messageConfig) = $this->makeValidateCache($nowActionKey);
        $attrConfig = Cache::get($nowActionKey . 'attr') ?? [];

        return $attrConfig;
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages()
    {
        $nowActionKey = Route::currentRouteAction();
        $messageConfig = Cache::get($nowActionKey . 'messages') ?? [];
        if ($messageConfig = Cache::get($nowActionKey . 'messages')) {
            return $messageConfig;
        }
        list($ruleConfig, $attrConfig, $messageConfig) = $this->makeValidateCache($nowActionKey);
        $messageConfig = Cache::get($nowActionKey . 'messages') ?? [];

        return $messageConfig;
    }

    //更新路由验证的缓存
    public function makeValidateCache($nowActionKey)
    {
        $ruleConfigs = config('makeValidate');
        $ruleConfig = [];
        $attrConfig = [];
        foreach ($ruleConfigs as $param => $rule) {
            if (in_array($nowActionKey, $rule[0])) {
                $ruleConfig[$param] = $rule[1] ?? [];
                $attrConfig[$param] = $rule[2] ?? [];
                $messageConfig[$rule[3][0] ?? []] = $rule[3][1] ?? [];
            }
        }
        Cache::set($nowActionKey . 'rule', $ruleConfig);
        Cache::set($nowActionKey . 'attr', $attrConfig);
        Cache::set($nowActionKey . 'messages', $messageConfig);

        return [$ruleConfig, $attrConfig, $messageConfig];
    }
}
