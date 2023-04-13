<?php

return [
    'name'         => [
        [\App\Http\Controllers\TestController::class . '@index'],
        ['bail', 'required', 'string'],
        '用户姓名',
    ],
    'gender'       => [
        [\App\Http\Controllers\TestController::class . '@index'],
        ['bail', 'required', 'int'],
        '用户性别',
    ],
    'card_no'      => [
        [\App\Http\Controllers\TestController::class . '@index'],
        ['bail', 'required', 'string'],
        '身份证号',
    ],
    'address'      => [
        [\App\Http\Controllers\TestController::class . '@index'],
        ['bail', 'required', 'string'],
        '患者地址',
    ],
    'village_code' => [
        [\App\Http\Controllers\TestController::class . '@index'],
        ['bail', 'required', 'string'],
        '村code',
    ],
    'town_code'    => [
        [\App\Http\Controllers\TestController::class . '@index'],
        ['bail', 'required', 'string'],
        '镇code',
    ],
    'is_risk_cro'  => [
        [\App\Http\Controllers\TestController::class . '@index'],
        ['bail', 'required', 'string'],
        '是否添加到风险人群',
    ],
];
