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
