[English](README.md) | 简体中文

# GUPO AUTOMAKE SDK for PHP
实现了自动生成列表,验证器优化等功能
## 安装

### Composer

```bash
composer require lsg/auto-screen

php artisan vendor:publish --provider="Lsg\AutoScreen\AutoScreenServiceProvider"

config/app.php
增加 
//自动查询脚本
Lsg\AutoScreen\AutoScreenServiceProvider::class
```


## 列表使用说明
```bash

php artisan task:make_list AutoList
//在app/Lists下面生成了自动列表代码
使用方法:
 *      0.声明一个列表路由(比如/api/patient/list)
 *      1.在controller层$service->list($itemCode),
 *      2.在service层use当前类

```

## 验证器使用说明
controller层需要引入MakeValidateRequest
```php  
<?php
namespace App\Http\Controllers\Clinical;
use Lsg\AutoScreen\Support\MakeValidateRequest;

class PatientController extends BaseController
{
    public function createPatients(MakeValidateRequest $request)
    {
        $user = request()->user;
        $card_no = $request->input('card_no', '');
        $gender = get_idcard_sex($card_no);
        $birth_year = get_idcard_year($card_no);
        if (Patients::where('users_id', $user->id)->where('card_no', $card_no)->exists()) {
            throw new ApiException('患者已存在,请勿重复添加');
        }
        $res = Patients::makeCreate(['users_id' => $user->id, 'gender' => $gender, 'birth_year' => $birth_year]);

        return success($res);
    }
```
config/makeValidate.php增加需要验证的类
```php  
<?php
use App\Http\Controllers\TestController;
use App\Http\Controllers\Clinical\PatientController;
return [
    'name'    => [
        [TestController::class . '@index',PatientController::class . '@createPatients'],
        ['bail', 'required', 'string'],
        '用户姓名',
    ],
    'phone'   => [
        [TestController::class . '@index'],
        ['bail', 'required', new Lsg\AutoScreen\Rules\PhoneRule],
        '手机号',
    ],
    'card_no' => [
        [TestController::class . '@index',PatientController::class . '@createPatients'],
        ['bail', 'required', new Lsg\AutoScreen\Rules\IdCardRule],
        '身份证号',
    ],
];
```
```
//更新自动验证缓存
php artisan task:make_validate
```
## 缓存中间件使用说明
App/Http/Kernel注册中间件
'validate.make'        => \Lsg\AutoScreen\Middleware\ValidateMake::class,
```php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        // \App\Http\Middleware\TrustProxies::class,
        // \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // 'throttle:api',
            // \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth'                 => \App\Http\Middleware\Authenticate::class,
        //缓存中间件
        'cache.make'           => \App\Http\Middleware\CacheMake::class,
        //入参验证器
        'validate.make'        => \Lsg\AutoScreen\Middleware\ValidateMake::class,
    ];
}
```
路由引入中间件
参数1是缓存时间
参数2是缓存key(在使用redis缓存的情况下仅仅对当前路由与入参作为缓存的key可能会导致redis缓存key重复)
```php  
<?php
Route::group([
            'middleware' => ['cache.make:1,cancers'],
        ], function () {
            // 顶部六要素统计
            Route::get('top_six_element', [WorkbenchController::class, 'topSixElement']);
        });

```
## 发行说明

每个版本的详细更改记录在[发行说明](./ChangeLog.txt)中。

## 相关

* [最新源码](https://github.com/linsonggao/AutoScreen)

## 许可证


Copyright (c) 2022-present, linsonggao All rights reserved