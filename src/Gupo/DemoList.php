<?php

namespace Lsg\AutoScreen\Gupo;

use App\Models\Dws\AggrGjaRskElmt;
use App\Models\Patient;

/**
 * 这是一个demo
 */
trait DemoList
{
    use \Lsg\AutoScreen\Gupo\BaseList;

    //缓存时间
    protected static $cacheExpire = 1800;

    //业务模型
    protected static $bussinessModel = Patient::class;

    //业务字段
    protected static $bussinessColumn = [
        'is_tel_call',
        'sms_msg_num',
        'not_trtmt_is_tel_call',
        'not_trtmt_sms_msg_num',
        'wait_oprt_is_tel_call',
        'wait_oprt_sms_msg_num',
        'not_vcn_is_tel_call',
        'not_vcn_sms_msg_num',
        'is_removed',
        'is_lost_contact',
        'is_loss_follow',
        'is_treatment',
        'treatment_time',
        'treatment_way',
        'untreated_cause',
        'remark',
    ];

    //基础字段转换
    protected static $baseColumnCs = [
        'id'           => 'user_id',
        'name'         => 'ptt_nm',
        'gender'       => 'gdr',
        'mobile'       => 'tel_no',
        'card_no'      => 'id_crd_no',
        'address'      => 'addr',
        'village_name' => 'curr_addr_vlg_nm',
        'village_code' => 'curr_addr_vlg_cd',
        'town_name'    => 'curr_addr_twn_nm',
        'town_code'    => 'curr_addr_twn_cd',
    ];

    //基础查询
    protected static $baseWhere = ['is_宫颈癌导致死亡' => 0, 'year' => '全部'];

    //基础查询字段
    protected static $baseSelect = ['user_id', 'gdr', 'age', 'ptt_nm', 'slf_tel_no', 'id_crd_no', 'addr', 'curr_addr_twn_cd', 'curr_addr_twn_nm', 'curr_addr_vlg_cd', 'curr_addr_vlg_nm', 'is_宫颈癌手术', 'oprt_mdc_org_cd_宫颈癌'];

    //年龄范围人群
    protected $is_age_flg = [
        'is_hpv_vcn'            => 'is_hpv_vcn', //是否接种hpv 0否 1是
        'last_tp_hpv_vcn'       => 'last_tp_hpv_vcn', //末次hpv疫苗类型
        'last_org_nm_hpv_vcn'   => 'last_org_nm_hpv_vcn', //末次hpv疫苗机构名称
        'last_inclt_tm_hpv_vcn' => 'last_inclt_tm_hpv_vcn', //末次hpv疫苗时间
    ];

    /**
     * 自动化列表
     * @param mixed $method
     * @param mixed $requestData
     */
    public function tableList($method, $requestData)
    {
        $list =
          match ($method) {
              //搜索指定年龄范围人群
              'is_age_flg' => AggrGjaRskElmt::makeList(
                  orderBy: [['user_id', 'desc']],
                  select: $this->addSelect(),
                  requestData: $requestData
              ),
              default      => [
                  'list'  => [],
                  'total' => 0,
              ]
          };

        return $list;
    }
}
