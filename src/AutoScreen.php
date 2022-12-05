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
			if ($searchValue && in_array($searchKey, $columnList) && $searchValue != $default && !in_array($searchKey, $configSearchKeys)) {
				$type = Schema::getColumnType($table, $searchKey);
				//时间筛选,时间格式并且是数组
				if (is_array($searchValue) && ($type == 'datetime' || $type == 'date')) {
					$q->where($searchKey, '>=', $searchValue[0] . " 00:00:00")->where($searchKey, '<=', $searchValue[0] . " 23:59:59");
				} else if ($type == 'string' && !in_array($searchKey, config('automake.string_equal'))) { //如果是字符串并且默认为like
					$q->where($searchKey, 'like', '%' . $searchValue . '%');
				}
			}
		}
		return $q;
	}
	/**
	 * 填写过滤条件
	 */
	public function makeAutoPageList($screen = [], $select = ["*"])
	{
		$this->select = $select;
		$q = $this->makeAutoQuery();
		if ($screen) {
			foreach ($screen as $key => $value) {
				$q->where($key, $value);
			}
		}
		$q->orderBy('id', 'desc');
		$page =  request()->input('page', 1);
		$per_page = request()->input('per_page', 15);

		$list = $q->paginate($per_page, ['*'], 'page', $page)->toArray();

		//枚举值类型转换
		$enm_str = 'automake.' . $this->table . '_enums_arr';
		$enm_arr = config($enm_str) ?? '';
		if ($enm_arr && is_array($enm_arr)) {
			foreach ($list['data'] as &$list_value) {
				foreach ($enm_arr as $k => $value) {
					# code...
					if (isset($list_value[$k])) {
						$list_value[$k . '_str'] = $value[$list_value[$k]];
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
	 */
	public function doAutoUpdate($onlyUpdate = ['*'], $except = false)
	{
		$only = false;
		if (count($onlyUpdate) == 1 && $onlyUpdate[0] == '*') {
			$updateArr = request()->all();
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
