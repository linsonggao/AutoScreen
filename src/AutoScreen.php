<?php

namespace Lsg\AutoScreen;

use Illuminate\Support\Facades\Schema;

class AutoScreen
{
	protected $query;
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
		$table = ($this->query)->getTable();
		$q = ($this->query)->query();
		$default = config('automake.default');
		$configSearchKeys = config('automake.search_key');
		$columnList = Schema::getColumnListing($table);
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
			if ($searchValue && in_array($searchKey, $columnList) && $searchValue != $default && !in_array($searchKey, $configSearchKeys)) {
				$type = Schema::getColumnType($table, $searchKey);
				//时间筛选,时间格式并且是数组
				if (is_array($searchValue) && $type == 'datetime') {
					$q->where($searchKey, '>=', $searchValue[0] . " 00:00:00")->where($searchKey, '<=', $searchValue[0] . " 23:59:59");
				} else if ($type == 'string' && !in_array($searchKey, config('automake.string_equal'))) { //如果是字符串并且默认为like
					$q->where($searchKey, 'like', '%' . $searchValue . '%');
				}
			}
		}
		return $q;
	}
	/**
	 * 自动更新表字段
	 * $query = new Admin();
	 * $res = AutoMake::getQuery($query)->doAutoUpdate();
	 */
	public function doAutoUpdate()
	{
		$updateArr = request()->all();
		if (!isset($updateArr['id'])) {
			return false;
		}
		$table = ($this->query)->getTable();
		$q = ($this->query)->query();
		$columnList = Schema::getColumnListing($table);
		foreach ($updateArr as  $updateKey => $updateValue) {
			if (in_array($updateKey, $columnList)) {
				$q->where('id', $updateArr['id'])->update([
					$updateKey => $updateValue
				]);
			}
		}
		return true;
	}
}
