<?php
return [
  //默认不筛选的上传值
  'default' => -1,
  //默认不做模糊匹配的字段
  'string_equal' => [],
  //表多个模糊匹配字段配置,提交
  'search_key' => ['search_key'],
  //表多个模糊匹配字段配置,表字段
  'search_value' => ['name', 'nickname', 'id', 'mobile'],
  //字段说明枚举.需要不同的表字段独立
  'intestine_patients_enums_arr' => [
    'is_sign' => [0 => '未签约', 1 => '已签约'],
    'follow_up_status' => [-1 => '--', 0 => '未随访', 1 => '已随访', 2 => '超时未随访']
  ]
];
