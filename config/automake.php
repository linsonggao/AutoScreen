<?php
return [
  //默认不筛选的上传值
  'default' => -1,
  //默认不做模糊匹配的字段
  'string_equal' => [],
  //表多个模糊匹配字段配置,提交
  'search_key' => ['search_key'],
  //表多个模糊匹配字段配置,表字段
  'search_value' => ['name', 'nickname', 'id', 'mobile']
];
