<?php
return [
    'sql_log_debug'                                       => env('SQL_LOG_DEBUG', false), //sql日志
    'cache_time'                                          => 6, //数据字段缓存-反正重复筛查
    //默认不筛选的上传值
    'default'                                             => -1,
    //默认不做模糊匹配的字符串(varchar)字段
    'string_equal'                                        => ['rctscp_check_tp', 'curr_addr_twn_cd', 'curr_addr_vlg_cd', 'year'],
    'yuhuan_spcl_dses_clrtl_cncr_list_string_equal'       => ['rctscp_check_tp', 'curr_addr_twn_cd', 'curr_addr_vlg_cd'],
    //表多个模糊匹配字段配置,提交-暂时不支持多个值
    'search_key'                                          => ['search_key', 'name', 'mobile', 'phone'],
    //表多个模糊匹配字段配置,表字段
    'search_value'                                        => ['name', 'mobile'],
    //模糊查询需要关联的表
    'like_join_table'                                     => ['card_name_mobiles', 'card_no', 'card_no'],
    //字段说明枚举.需要不同的表字段独立
    'intestine_patients_enums_arr'                        => [
        'is_sign'                  => [0 => '未签约', 1 => '已签约'],
        'follow_up_status'         => [0 => '未随访', 1 => '已随访', 2 => '超时未随访'],
        'gender'                   => [0 => '保密', 1 => '男', 2 => '女', '男' => '男', '女' => '女'],
        'is_questionnaire_scn_flg' => [1 => '普筛', 0 => '机会性筛查'],
    ],
    //字段说明枚举.需要不同的表字段独立//表名_enums_arr
    'questionnaires_logs_enums_arr'                       => [
        'client_check_status'  => [0 => '未查看', 1 => '已查看'],
        'client_submit_status' => [0 => '未提交', 1 => '已提交'],
        'result_send_status'   => [0 => '暂无报告', 1 => '已生成报告'],
        'type'                 => ['default' => '问卷', 'followup' => '随访', 'gauge' => '量表', 'psychology' => '心理问卷', 'satisfaction' => '满意度问卷'],
        'channel'              => [1 => '健康地图'],
        'source'               => [1 => '后台推送', 2 => '家医推送'],
        'gender'               => [0 => '保密', 1 => '男', 2 => '女', '男' => '男', '女' => '女'],
    ],
    //字段说明枚举,表名_enums_arr
    'intestine_patients_cure_logs_enums_arr'              => [
        'risk_level'           => [0 => '--', 1 => '低风险', 2 => '中风险', 3 => '高风险'],
        'dbe_status'           => [0 => '肠镜待检查', 1 => '肠镜检查完成'],
        'operate_status'       => [0 => '待手术', 1 => '已经完成手术'],
        'operate_after_status' => [0 => '未复诊', 1 => '已复诊'],
        'gender'               => [0 => '保密', 1 => '男', 2 => '女', '男' => '男', '女' => '女'],
    ],
    //某表需要判断between的值，表名_between_arr
    'intestine_patients_between_arr'                      => ['year_bth', 'operate_at', 'age', 'sms_msg_num', 'not_trtmt_sms_msg_num', 'wait_oprt_sms_msg_num'],
    //某表需要判断between的值，表名_between_arr
    'questionnaires_logs_between_arr'                     => ['year_bth'],
    //某表需要判断between的值，表名_between_arr
    'intestine_patients_diag_logs_between_arr'            => ['year_bth', 'age'],
    //某表需要判断between的值，表名_between_arr
    'intestine_patients_cure_logs_between_arr'            => ['year_bth', 'age'],
    //需要判断大于的值
    'intestine_patients_gt_arr'                           => ['age', 'year_bth', 'sms_msg_num', 'not_trtmt_sms_msg_num', 'wait_oprt_sms_msg_num'],
    //需要判断小于的值
    'intestine_patients_lt_arr'                           => ['age', 'year_bth'],
    //二位数组需要多重判断，表名_in_multi
    'intestine_patients_in_multi'                         => ['age'],
    'yuhuan_spcl_dses_clrtl_cncr_list_in_multi'           => ['age'], //需要提交参数为 age[0][0],age[0][1]或者age[0][0] = 1,100
    //某表需要判断between的值，表名_between_arr
    'yuhuan_spcl_dses_clrtl_cncr_list_between_arr'        => ['age'], //
    'yuhuan_spcl_dses_clrtl_cncr_list_gt_arr'             => ['age'],
    'patients_between_arr'                                => ['age'],
    'patients_between_gt_arr'                             => ['age'],
    'patients_in_multi'                                   => ['age'],
    'intestine_patients_no_request_default'               => ['year' => '全部'],
    'yuhuan_spcl_dses_clrtl_cncr_list_no_request_default' => ['year' => '全部'],
];
