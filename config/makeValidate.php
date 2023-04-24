<?php
use App\Http\Controllers\TestController;

return [
    'name'    => [
        [TestController::class . '@index'],
        ['bail', 'required', 'string'],
        '用户姓名',
        '姓名字段必须',
    ],
    'phone'   => [
        [TestController::class . '@index'],
        ['bail', 'required', new Lsg\AutoScreen\Rules\PhoneRule],
        '手机号',
        '手机号必须',
    ],
    'card_no' => [
        [TestController::class . '@index'],
        ['bail', 'required', new Lsg\AutoScreen\Rules\IdCardRule],
        '身份证号',
        '身份证号必须',
    ],
];
