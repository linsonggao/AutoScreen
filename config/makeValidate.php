<?php

use App\Http\Controllers\Api\ApplyController;
use Lsg\AutoScreen\Rules\IdCardRule;
use Lsg\AutoScreen\Rules\PhoneRule;

return [
    'account_id' => [
        [
            ApplyController::class . '@getOrgDoc',
        ],
        ['sometimes', 'required', new IdCardRule],
        '身份证号',
        ['account_id.required' => '身份证号缺失'],
    ],
    'card_no'    => [
        [
            ApplyController::class . '@postApply',
            ApplyController::class . '@postRelationApply',
        ],
        ['bail', 'required', new IdCardRule],
        '身份证号',
        ['card_no.required' => '身份证号缺失'],
    ],
    'tel'        => [
        [
            ApplyController::class . '@postApply',
        ],
        ['bail', 'nullable', new PhoneRule],
        '手机号',
        ['tel.required' => '手机号缺失'],
    ],
    'fml_org_cd' => [
        [
            ApplyController::class . '@postApply',
        ],
        ['bail', 'required', 'string'],
        '家医所属机构编码',
        ['fml_org_cd.required' => '家医所属机构编码缺少'],
    ],
];
