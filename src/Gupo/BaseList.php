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
        $model = new self::$bussinessModel;
        /**
         * 缓存
         */
        $cacheKey = 'list' . json_encode(request()->all());
        $cardScreenArr = []; //身份证数组

        $noCsItems = self::$loseBaseColumnCsItems;
        $businessArrs = array_intersect(array_keys(request()->all()), self::$bussinessColumn); //是否存在业务字段需要查询
        if (Cache::has($cacheKey) && env('APP_ENV') !== 'local' && !$businessArrs) {
            $listJson = Cache::get($cacheKey);
            $list = json_decode($listJson, true);
        } else {
            /**
             * 修改入参
             */
            $requestData = [];
            $requestAll = request()->all();
            foreach ($requestAll as $key => $value) {
                //如果不是业务模型需要转换
                if (!in_array($method, $noCsItems)) {
                    if (isset(self::$baseColumnCs[$key])) {
                        $new_key = self::$baseColumnCs[$key] ?? $key;
                    } else {
                        $new_key = $key;
                    }
                } else {
                    $new_key = $key;
                }
                //附加项目转换
                $allItemKeys = $this->{$method};
                if (isset($allItemKeys[$new_key])) {
                    $new_key = $allItemKeys[$new_key];
                }
                $requestData[$new_key] = $value;
            }
            //数据部表无业务字段
            //搜索业务字段的情况下，过滤业务字段的人群
            if (!in_array($method, $noCsItems)) {
                foreach (self::$bussinessColumn as $value) {
                    if (isset($requestAll[$value])) {
                        $patientsAll = $model->makeList(requestData: ['page' => 1, 'per_page' => 99999999, ...$requestAll]);
                        $allListArr = $patientsAll['list']->toArray();
                        $inAllCardNo = array_column($allListArr, 'card_no');
                        $cardScreenArr = array_column($allListArr, null, 'card_no');
                        $requestData['id_crd_no'] = [1, ...$inAllCardNo];
                        break;
                    }
                }
            }

            $list = $this->tableList($method, $requestData);
            // 缓存用户数据
            Cache::put($cacheKey, json_encode($list), self::$cacheExpire);
        }
        //新增业务数据字段
        //未搜索业务字段的情况下，增加业务字段
        if (!$cardScreenArr) {
            $listArr = json_decode(json_encode($list['list']), true);
            $inCrdArr = array_column($listArr, 'id_crd_no');
            $baseBussinessSelect = array_values(array_intersect($this->{$method}, self::$bussinessColumn));
            $arrData = $model->select(['id', 'card_no', ...$baseBussinessSelect])->where('year', '全部')->whereIn('card_no', $inCrdArr)->get()->toArray();
            $cardScreenArr = array_column($arrData, null, 'card_no');
        }
        //取交集
        if (!in_array($method, $noCsItems)) {
            foreach ($list['list'] as $key => $value) {
                foreach (self::$bussinessColumn as $k => $column) {
                    if (in_array($column, $this->{$method})) {
                        if (isset($cardScreenArr[$value['id_crd_no']])) {
                            if (isset($cardScreenArr[$value['id_crd_no']][$column]) || is_null($cardScreenArr[$value['id_crd_no']][$column])) {
                                $list['list'][$key][$column] = $cardScreenArr[$value['id_crd_no']][$column];
                            }
                        }
                    }
                }
                if (isset($cardScreenArr[$value['id_crd_no']])) {
                    $list['list'][$key]['user_id'] = $cardScreenArr[$value['id_crd_no']]['id'] ?? $list['list'][$key]['user_id'];
                }
            }
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
        foreach (self::$bussinessColumn as $value) {
            if ($key = array_search($value, $addSelect)) {
                unset($addSelect[$key]);
            }
        }
        //基础表基础字段还是关联表的基础字段
        $noCsItems = self::$loseBaseColumnCsItems;
        if (in_array($appendItem, $noCsItems)) {
            return array_merge(self::$loseCsBaseSelect, $addSelect);
        } else {
            return array_merge(self::$baseSelect, $addSelect);
        }
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
                if (isset($item[$mapValue])) {
                    $listArr[$key]['items'][$k] = $listArr[$key][$mapValue];
                    continue;
                } else {//未同步的数据、或者为null的数据、或者不存在的字段
                    $listArr[$key]['items'][$k] = null;
                }
            }
        }
        $list['list'] = $listArr;

        return $list;
    }
}
