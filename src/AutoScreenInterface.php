<?php

namespace Lsg\AutoScreen;

interface AutoScreenInterface
{
    /**
     * 返回model实例
     * @param mixed $query
     * @return object
     */
    public function getQuery($query): object;

    /**
     * 返回处理过的model实例
     * 调用方法
     * $query = new Admin();
     * $res = AutoMake::getQuery($query)->makeAutoQuery();
     * $res->get()->toArray();
     * @return object
     */
    public function makeAutoQuery(): object;

    /**
     * 填写过滤条件
     * @param array $screen 传过滤字段
     * @param mixed $select 传只筛查字段
     * @param array $loseWhere 传不筛查的字段数组
     * @param bool $pageCustom 分页的问题
     * @return array
     */
    public function makeAutoPageList($screen, $select, $loseWhere, $pageCustom): array;

    /**
     * 填写过滤条件
     * @param array $screen 传过滤字段
     * @param mixed $select 传只筛查字段
     * @param array $loseWhere 传不筛查的字段数组
     * @param bool $pageCustom 分页的问题
     * @return array
     */
    public function makeCustomPageList(): array;

    /**
     * 自动更新表字段
     * $query = new Admin();
     * $res = AutoMake::getQuery($query)->doAutoUpdate();
     * @param array $onlyUpdate 传只更新字段
     * @param mixed $except 传不更新的字段
     * @return bool true/false
     */
    public function doAutoUpdate($onlyUpdate, $except): bool;
}
