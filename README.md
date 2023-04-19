[English](README.md) | 简体中文

# GUPO AUTOMAKE SDK for PHP
实现了自动生成列表,验证器优化等功能
## 安装

### Composer

```bash
composer require lsg/auto-screen
```


## 使用说明
```bash
php artisan vendor:publish --provider="Lsg\AutoScreen\AutoScreenServiceProvider"
使用方法
config/app.php
增加 
//自动查询脚本
Lsg\AutoScreen\AutoScreenServiceProvider::class

php artisan task:make_list AutoList
//在app/Lists下面生成了自动列表代码
使用方法:
 *      0.声明一个列表路由(比如/api/patient/list)
 *      1.在controller层$service->list($itemCode),
 *      2.在service层use当前类

//验证器用法
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

config/makeValidate.php增加需要验证的类
<?php
use App\Http\Controllers\TestController;

return [
    'name'    => [
        [TestController::class . '@index'],
        ['bail', 'required', 'string'],
        '用户姓名',
    ],
    'card_no' => [
        [TestController::class . '@index'],
        ['bail', 'required', new Lsg\AutoScreen\Rules\IdCardRule],
        '身份证号',
    ],
];
//更新自动验证缓存
php artisan task:make_validate

```
## 发行说明

每个版本的详细更改记录在[发行说明](./ChangeLog.txt)中。

## 相关

* [最新源码](https://github.com/linsonggao/AutoScreen)

## 许可证


Copyright (c) 2009-present, linsonggao All rights reserved