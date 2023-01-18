<?php

namespace Lsg\AutoScreen\Gupo;

use Illuminate\Support\Facades\Cache;

/**
 * aop列表查询框架
 */
trait BaseList
{
    /**
     * 自动化列表
     * @param mixed $method
     */
    public function list($method)
    {
        $model = new $this->bussinessModel;
        /**
         * 缓存
         */
        $cacheKey = 'list' . json_encode(request()->all());
        $cardScreenArr = []; //身份证数组
        if (Cache::has($cacheKey) && env('APP_ENV') !== 'local') {
            $listJson = Cache::get($cacheKey);
            $list = json_decode($listJson, true);
        } else {
            /**
             * 修改入参
             */
            $requestData = [];
            $requestData['year'] = '全部';
            $requestAll = request()->all();
            foreach ($requestAll as $key => $value) {
                $new_key =
                  match ($key) {
                      'id'           => $this->baseColumnCs['id'],
                      'name'         => $this->baseColumnCs['name'],
                      'gender'       => $this->baseColumnCs['gender'],
                      'mobile'       => $this->baseColumnCs['mobile'],
                      'card_no'      => $this->baseColumnCs['card_no'],
                      'address'      => $this->baseColumnCs['address'],
                      'village_name' => $this->baseColumnCs['village_name'],
                      'village_code' => $this->baseColumnCs['village_code'],
                      'town_name'    => $this->baseColumnCs['town_name'],
                      'town_code'    => $this->baseColumnCs['town_code'],
                      default        => $key,
                  };
                //附加项目转换
                $allItemKeys = $this->{$method};
                if (isset($allItemKeys[$new_key])) {
                    $new_key = $allItemKeys[$new_key];
                }
                if ($key == 'year') {
                    self::$baseWhere = self::$baseWhereNoYear;
                }
                $requestData[$new_key] = $value;
            }
            //数据部表无业务字段
            foreach ($this->bussinessColumn as $value) {
                if (isset($requestAll[$value])) {
                    $patientsAll = $model->makeList(requestData: ['page' => 1, 'per_page' => 99999999, ...$requestAll]);
                    $allListArr = $patientsAll['list']->toArray();
                    $inAllCardNo = array_column($allListArr, 'card_no');
                    $cardScreenArr = array_column($allListArr, null, 'card_no');
                    $requestData['id_crd_no'] = [1, ...$inAllCardNo];
                    break;
                }
            }
            $list = $this->tableList($method, $requestData);
            // 缓存用户数据
            Cache::put($cacheKey, json_encode($list), $this->cacheExpire);
        }
        //新增业务数据字段
        if (!$cardScreenArr) {
            $listArr = json_decode(json_encode($list['list']), true);
            $inCrdArr = array_column($listArr, 'id_crd_no');
            $baseBussinessSelect = array_values(array_intersect($this->{$method}, $this->bussinessColumn));
            $arrData = $model->select(['id', 'card_no', ...$baseBussinessSelect])->whereIn('card_no', $inCrdArr)->get()->toArray();
            $cardScreenArr = array_column($arrData, null, 'card_no');
        }
        //取交集
        foreach ($list['list'] as $key => $value) {
            foreach ($this->bussinessColumn as $k => $column) {
                if (in_array($column, $this->{$method})) {
                    if (isset($cardScreenArr[$value['id_crd_no']][$column]) || is_null($cardScreenArr[$value['id_crd_no']][$column])) {
                        $list['list'][$key][$column] = $cardScreenArr[$value['id_crd_no']][$column];
                    }
                }
            }
            $list['list'][$key]['user_id'] = $cardScreenArr[$value['id_crd_no']]['id'] ?? $list['list'][$key]['user_id'];
        }
        $res = $this->appendItems($list);

        return $res;
    }

    //固定查询项目
    protected function addSelect()
    {
        $appendItem = debug_backtrace()[1]['args'][0];
        $itemMap = $this->{$appendItem} ?? [];
        $addSelect = array_values($itemMap);
        if (isset(self::$itemDoEqual[$appendItem])) {
            $addSelect[] = self::$itemDoEqual[$appendItem];
        }
        //过滤非表字段
        if ($key = array_search('oprt_info_url', $addSelect)) {
            unset($addSelect[$key]);
        }
        foreach ($this->bussinessColumn as $value) {
            if ($key = array_search($value, $addSelect)) {
                unset($addSelect[$key]);
            }
        }

        return array_merge(self::$baseSelect, $addSelect);
    }

    //固定基础查询条件
    protected static function addWhere($where)
    {
        return array_merge(self::$baseWhere, $where);
    }

    /**
     * 追加自定义items信息
     *
     * @param array $list 原始列表
     * @param array $itemMap item项映射
     * @return array
     */
    protected function appendItems(array $list, array $itemMap = []): array
    {
        $appendItem = debug_backtrace()[1]['args'][0];
        $itemMap = $this->{$appendItem} ?? [];
        if (is_array($list['list'])) {
            $listArr = $list['list'];
        } else {
            $listArr = $list['list']->toArray();
        }
        foreach ($listArr as $key => $item) {
            foreach ($itemMap as $k => $mapValue) {
                if (isset($item[$mapValue]) || is_null($item[$mapValue])) {
                    $listArr[$key]['items'][$k] = $listArr[$key][$mapValue];
                    continue;
                }
            }
        }
        $list['list'] = $listArr;

        return $list;
    }
}
