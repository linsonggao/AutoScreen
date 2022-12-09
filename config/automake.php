<?php
return [
  //默认不筛选的上传值
  'default' => -1,
  //默认不做模糊匹配的字符串(varchar)字段
  'string_equal' => [],
  //表多个模糊匹配字段配置,提交-暂时不支持多个值
  'search_key' => ['search_key'],
  //表多个模糊匹配字段配置,表字段
  'search_value' => ['name', 'nickname', 'id', 'mobile'],
  //字段说明枚举.需要不同的表字段独立
  'intestine_patients_enums_arr' => [
    'is_sign' => [0 => '未签约', 1 => '已签约'],
    'follow_up_status' => [0 => '未随访', 1 => '已随访', 2 => '超时未随访'],
    'gender' => [0 => '保密', 1 => '男', 2 => '女']
  ],
  //字段说明枚举.需要不同的表字段独立//表名_enums_arr
  'questionnaires_logs_enums_arr' => [
    'client_check_status' => [0 => '未查看', 1 => '已查看'],
    'client_submit_status' => [0 => '未提交', 1 => '已提交'],
    'result_send_status' => [0 => '暂无报告', 1 => '已生成报告'],
    'type' => ['default' => '问卷', 'followup' => '随访', 'gauge' => '量表', 'psychology' => '心里问卷', 'satisfaction' => '满意度问卷'],
    'channel' => [1 => '健康地图'],
    'source' => [1 => '后台推送', 2 => '家医推送'],
    'gender' => [0 => '保密', 1 => '男', 2 => '女']
  ],
  //字段说明枚举,表名_enums_arr
  'intestine_patients_cure_logs_enums_arr' => [
    'risk_level' => [0 => '--', 1 => '低风险', 2 => '中风险', 3 => '高风险'],
    'dbe_status' => [0 => '肠镜待检查', 1 => '肠镜检查完成'],
    'operate_status' => [0 => '待手术', 1 => '已经完成手术'],
    'operate_after_status' => [0 => '未复诊', 1 => '已复诊'],
    'gender' => [0 => '保密', 1 => '男', 2 => '女']
  ],
  //某表需要判断between的值
  'intestine_patients_between_arr' => ['year_bth'],
  //需要判断大于的值
  'intestine_patients_gt_arr' => [],
  //需要判断小于的值
  'intestine_patients_gt_arr' => [],
];
