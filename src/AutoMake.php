<?php

namespace Lsg\AutoScreen;

use Illuminate\Support\Facades\Facade;

class AutoMake extends Facade
{
    /**
     * 调用中台接口服务 门面名称
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auto-screen';
    }
}
