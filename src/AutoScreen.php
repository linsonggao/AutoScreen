<?php

namespace Lsg\AutoScreen;

use Illuminate\Support\Facades\Schema;

class AutoScreen
{
	protected $query;
	protected $page;
	protected $per_page;
	protected $select;
	protected $table;
	protected $columnList;
	protected $requestData;
	protected $loseWhere;
	public function getQuery($query)
	{
		$this->query = $query;
		return $this;
	}
	/**
	 * 调用方法
	 * $query = new Admin();
	 * $res = AutoMake::getQuery($query)->makeAutoQuery();
	 * $res->get()->toArray();
	 */
	public function makeAutoQuery()
	{
		$this->table = $table = ($this->query)->getTable();
		$q = ($this->query)->query();
		$q->select($this->select);
		$default = config('automake.default');
		$configSearchKeys = config('automake.search_key');
		$this->columnList = $columnList = Schema::getColumnListing($table);
		$searchArr = request()->all();
		foreach ($searchArr as  $searchKey => $searchValue) {
			//不进行筛选的数组
			if ($this->loseWhere) {
				if (in_array($searchKey, $this->loseWhere)) {
					continue;
				}
			}
			//默认值
			$searchValue = request()->input($searchKey, $default);
			//多条件筛选
			if (in_array($searchKey, $configSearchKeys)) {
				$q->where(
					function ($query) use ($searchKey, $searchValue, $columnList, $table) {
						$search_values = config('automake.search_value');
						foreach ($search_values as $k => $config_search_name) {
							if (in_array($config_search_name, $columnList)) {
								$type = Schema::getColumnType($table, $config_search_name);
								if ($type == 'string' && !in_array($searchKey, config('automake.string_equal'))) {
									$query->where($config_search_name, 'like', '%' . $searchValue . '%');
								} else {
									$query->orWhere($config_search_name, $searchValue);
								}
							}
						}
					}
				);
			}
			//时间与字符串like
			if (($searchValue || $searchValue == 0) && in_array($searchKey, $columnList) && $searchValue != $default && !in_array($searchKey, $configSearchKeys)) {
				$type = Schema::getColumnType($table, $searchKey);
				//时间筛选,时间格式并且是数组
				if (is_array($searchValue) && ($type == 'datetime' || $type == 'date')) {
					$q->where($searchKey, '>=', $searchValue[0] . " 00:00:00")->where($searchKey, '<=', $searchValue[0] . " 23:59:59");
				} else if ($type == 'string' && !in_array($searchKey, config('automake.string_equal'))) { //如果是字符串并且默认为like
					$searchValue = str_replace('%', "\%", $searchValue);
					$q->where($searchKey, 'like', '%' . $searchValue . '%');
				} else if (($type == 'boolean' || $type == 'integer') && is_numeric($searchValue)) { //如果是int值则直接等于
					$q->where($searchKey, $searchValue);
				} else if (is_array($searchValue)) { //如果是数组的话需要分情况
					//枚举值类型转换
					$between_str = 'automake.' . $this->table . '_between_arr';
					$between_arr = config($between_str) ?? '';
					$gt_str = 'automake.' . $this->table . '_gt_arr';
					$gt_arr = config($gt_str) ?? '';
					$lt_str = 'automake.' . $this->table . '_lt_arr';
					$lt_arr = config($lt_str) ?? '';
					if ($between_arr && count($searchValue) >= 2) {
						$q->where($searchKey, '>=', $searchValue[0])->where($searchKey, '<=', $searchValue[1]);
					} else if ($gt_arr && count($gt_arr) >= 1) {
						$q->where($searchKey, '>=', $searchValue);
					} else if ($lt_arr && count($gt_arr) >= 1) {
						$q->where($searchKey, '<=', $searchValue);
					} else {
						$q->whereIn($searchKey, $searchValue);
					}
				} else if ($type == 'integer' && is_string($searchValue)) {
					$between_str = 'automake.' . $this->table . '_between_arr';
					$between_arr = config($between_str) ?? '';
					if (in_array($searchKey, $between_arr)) {
						$arr = explode(',', $searchValue);
						if ($arr) {
							$q->where($searchKey, '>=', $arr[0])->where($searchKey, '<=', $arr[1]);
						}
					}
				}
			}
		}
		//$q->where('test', 'test');
		return $q;
	}
	/**
	 * 填写过滤条件
	 * @param array $screen 传过滤字段
	 * @param mixed $select 传只筛查字段
	 * @param array $loseWhere 传不筛查的字段数组
	 */
	public function makeAutoPageList($screen = [], $select = ["*"], $loseWhere = [], $pageCustom = false)
	{
		$this->select = $select;
		$this->loseWhere = $loseWhere;
		$q = $this->makeAutoQuery();
		if ($screen) {
			foreach ($screen as $key => $value) {
				$q->where($key, $value);
			}
		}
		$q->orderBy('id', 'desc');
		$page =  request()->input('page', 1);
		$per_page = request()->input('per_page', 15);
		if ($pageCustom) {
			$list = $q->customPaginate(true, $per_page, $page)->toArray();
		} else {
			$list = $q->paginate($per_page, ['*'], 'page', $page)->toArray();
		}
		//枚举值类型转换
		$enm_str = 'automake.' . $this->table . '_enums_arr';

		$enm_arr = config($enm_str) ?? '';
		if ($enm_arr && is_array($enm_arr)) {
			foreach ($list['data'] ?? $list['list'] as &$list_value) {
				foreach ($enm_arr as $k => $value) {
					# code...
					if (isset($list_value[$k])) {
						$list_value[$k . '_str'] = $value[$list_value[$k]] ?? '未定义的枚举类型' . $k . ':' . $list_value[$k];
					}
				}
			}
		}
		return $list;
	}
	/**
	 * 自动更新表字段
	 * $query = new Admin();
	 * $res = AutoMake::getQuery($query)->doAutoUpdate();
	 * @param array $onlyUpdate 传只更新字段
	 * @param mixed $except 传不更新的字段
	 * @return bool true/false
	 */
	public function doAutoUpdate($onlyUpdate = ['*'], $except = false)
	{
		$only = false;
		$updateArr = request()->all();
		if (count($onlyUpdate) == 1 && $onlyUpdate[0] == '*') {
		} else {
			$only = true;
		}
		if (!isset($updateArr['id'])) {
			return false;
		}
		//如果设置了需要排除的字段
		$exceptArr = [];
		if (is_array($except)) {
			$exceptArr = $except;
		}
		$table = ($this->query)->getTable();
		$q = ($this->query)->query();
		$columnList = Schema::getColumnListing($table);
		foreach ($updateArr as  $updateKey => $updateValue) {
			if (in_array($updateKey, $exceptArr)) {
				continue;
			}
			if (in_array($updateKey, $columnList)) {
				//如果设置了只更新字段
				if ($only) {
					if (in_array($updateKey, $onlyUpdate)) {
						$q->where('id', $updateArr['id'])->update([
							$updateKey => $updateValue
						]);
					}
				} else {
					$q->where('id', $updateArr['id'])->update([
						$updateKey => $updateValue
					]);
				}
			}
		}
		return true;
	}
}
