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

        return $attrConfig;
    }
}
