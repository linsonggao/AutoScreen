<?php

namespace Lsg\AutoScreen;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AutoScreen extends AutoScreenAbstract implements AutoScreenInterface
{
	/**
	 * 调用方法
	 * $query = new Admin();
	 * $res = AutoMake::getQuery($query)->makeAutoQuery();
	 * $res->get()->toArray();
	 */
	public function makeAutoQuery(): object
	{
		//dd($this->query->from);
		if ($this->query instanceof Builder) {
			$tbArr = explode('.', $this->query->from);
			if (count($tbArr) == 2) {
				$this->table = $table = $tbArr[1];
				$schema = Schema::connection(strtolower($tbArr[0]) . '_mysql', $tbArr[0]);
				#$schema->getConnection()->setDatabaseName(strtolower($tbArr[0]));
			} else {
				$this->table = $table = $this->query->from;
				$schema = Schema::connection('mysql');
			}
			$q = ($this->query);
			//dd(123);
		} else {
			$this->table = $table = ($this->query)->getTable();
			$q = ($this->query)->query();
			$schema = Schema::connection('mysql');
		}
		$q->select($this->select);
		$default = config('automake.default');
		$configSearchKeys = config('automake.search_key');
		if ($this->query instanceof Builder) {
			//$this->columnList = $columnList = Schema::getColumnListing($this->query->from);
			$columnList = [];
			$this->columnList = $columnList = $schema->getColumnListing($table);
			//dd($columnList);
			//$res = DB::connection(strtolower($tbArr[0]) . '_mysql')->select('select column_name as `column_name` from information_schema.columns where table_schema =  "' . $tbArr[0] . '" and table_name = "' . $tbArr[1] . '"');
			// collect($res)->each(function ($item) use (&$columnList) {
			// 	$columnList[] = $item->column_name;
			// });
			//$this->columnList = $columnList;
			//dd($res);
		} else {
			$this->columnList = $columnList = Schema::getColumnListing($table);
		}
		$searchArr = $this->requestData ?: request()->all();
		foreach ($searchArr as  $searchKey => $searchValue) {
			//不进行筛选的数组
			if ($this->loseWhere) {
				if (in_array($searchKey, $this->loseWhere)) {
					continue;
				}
			}
			//默认值
			$searchValue = $searchArr[$searchKey] ?: $default; //request()->input($searchKey, $default);

			//优先判断二维数组，多条件
			if (is_array($searchValue) && count($searchValue) != count($searchValue, 1)) {
				$multi_str = 'automake.' . $this->table . '_in_multi';
				$multi_arr = config($multi_str) ?? [];
				if (in_array($searchKey, $multi_arr)) {
					//二维数组多重orwhere
					$q->where(
						function ($query) use ($searchKey, $searchValue) {
							foreach ($searchValue as $value) {
								if (count($value) < 2) {
									//逗号隔开 $age[0][0] = 1,10
									$strArr = explode(',', $value[0]);
									if (count($strArr) == 2) {
										$value = $strArr;
										//$age[][] = 1,10
										//字符串值between
										$query->orWhere(function ($q2) use ($searchKey, $value) {
											$q2->where($searchKey, '>=', $value[0])->where($searchKey, '<=', $value[1]);
										});
									} else {
										//$age[][] = 1
										//单个值大于
										$query->orWhere(function ($q2) use ($searchKey, $value) {
											$q2->where($searchKey, '>=', $value[0]);
										});
										continue;
									}
								}
							}
						}
					);
				}

				continue;
			}
			//判断json数组,多条件age[] = [18,20]
			if (is_array($searchValue) && is_array(json_decode($searchValue[0]))) {
				$multi_str = 'automake.' . $this->table . '_in_multi';
				$multi_arr = config($multi_str) ?? [];
				if (in_array($searchKey, $multi_arr)) {
					$q->where(
						function ($query) use ($searchKey, $searchValue) {
							foreach ($searchValue as $value) {
								//逗号隔开 $age[0][0] = 1,10
								$strArr = json_decode($value);
								if (count($strArr) == 2) {
									$value = $strArr;
									//$age[][] = 1,10
									//字符串值between
									$query->orWhere(function ($q2) use ($searchKey, $value) {
										$q2->where($searchKey, '>=', $value[0])->where($searchKey, '<=', $value[1]);
									});
								} else {
									//$age[][] = 1
									$value = $strArr;
									//单个值大于
									$query->orWhere(function ($q2) use ($searchKey, $value) {
										$q2->where($searchKey, '>=', $value[0]);
									});
									continue;
								}
							}
						}
					);
				}
				continue;
			}
			//多条件模糊匹配,name or mobile
			if (in_array($searchKey, $configSearchKeys)) {

				$q->where(
					function ($query) use ($searchKey, $searchValue, $columnList, $table, $schema) {
						$search_values = config('automake.search_value');
						foreach ($search_values as $k => $config_search_name) {
							if (in_array($config_search_name, $columnList)) {
								$type = $schema->getColumnType($table, $config_search_name);
								if ($type == 'string' && !in_array($searchKey, config('automake.string_equal'))) {
									$query->orWhere($config_search_name, 'like', '%' . $searchValue . '%');
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
				$between_str = 'automake.' . $this->table . '_between_arr';
				$between_arr = config($between_str) ?? [];
				if (is_array($searchValue)) { //如果是数组的话需要分情况
					//公司项目_tm结尾_at结尾_dt为时间
					if (
						strpos($searchKey, '_tm') ||
						strpos($searchKey, '_at') ||
						strpos($searchKey, '_dt') ||
						strpos($searchKey, '_date') ||
						strpos($searchKey, '_time') ||
						strpos($searchKey, 'tm_') !== false ||
						strpos($searchKey, 'dt_') !== false
					) {
						$q->where($searchKey, '>=', $searchValue[0] . " 00:00:00")->where($searchKey, '<=', $searchValue[1] . " 23:59:59");
						continue;
					}
					//枚举值类型转换
					$gt_str = 'automake.' . $this->table . '_gt_arr';
					$gt_arr = config($gt_str) ?? '';
					$lt_str = 'automake.' . $this->table . '_lt_arr';
					$lt_arr = config($lt_str) ?? '';
					if ($between_arr && count($searchValue) >= 2 && in_array($searchKey, $between_arr)) { //age[]大于2个值的时候
						$q->where($searchKey, '>=', $searchValue[0])->where($searchKey, '<=', $searchValue[1]);
					} else if ($between_arr && count($searchValue) == 1 && strpos($searchValue[0], ',') && in_array($searchKey, $between_arr)) { //age[] = 1,100的时候
						$ageArr = explode(',', $searchValue[0]);
						if (count($ageArr) == 2) {
							$q->where($searchKey, '>=', $ageArr[0])->where($searchKey, '<=', $ageArr[1]);
						}
					} else if ($gt_arr && in_array($searchKey, $gt_arr)) { //age[]=18，大于18
						$q->where($searchKey, '>=', $searchValue);
					} else if ($lt_arr && in_array($searchKey, $lt_arr)) {
						$q->where($searchKey, '<=', $searchValue);
					} else { //默认wherein
						$q->whereIn($searchKey, $searchValue);
					}
					continue;
				}
				$type = $schema->getColumnType($table, $searchKey);
				if (in_array($searchKey, config('automake.string_equal'))) {
					$q->where($searchKey, $searchValue);
					continue;
				}
				$table_string_equal = 'automake.' . $this->table . '_string_equal';
				if (is_array(config($table_string_equal))) {
					if (in_array($searchKey, config($table_string_equal))) {
						$q->where($searchKey, $searchValue);
						continue;
					}
				}
				//时间筛选,时间格式并且是数组
				if (is_array($searchValue) && ($type == 'datetime' || $type == 'date')) {
					$q->where($searchKey, '>=', $searchValue[0] . " 00:00:00")->where($searchKey, '<=', $searchValue[1] . " 23:59:59");
				} else if (($type == 'string' || $type == 'text') && !in_array($searchKey, config('automake.string_equal'))  &&
					!in_array($searchKey, $between_arr)
				) { //如果是字符串并且默认为like

					$searchValue = str_replace('%', "\%", $searchValue);
					$q->where($searchKey, 'like', '%' . $searchValue . '%');
				} else if (($type == 'boolean' || $type == 'integer') && is_numeric($searchValue)) { //如果是int值则直接等于
					$q->where($searchKey, $searchValue);
				} else if ($type == 'integer' && is_string($searchValue)) {
					$between_str = 'automake.' . $this->table . '_between_arr';
					$between_arr = config($between_str) ?? '';
					if (is_array($between_arr) && in_array($searchKey, $between_arr)) {
						$arr = explode(',', $searchValue);
						if ($arr) {
							$q->where($searchKey, '>=', $arr[0])->where($searchKey, '<=', $arr[1]);
						}
					}
				}
			}
		}
		#$q->where('test', 'test');
		return $q;
	}
	/**
	 * 填写过滤条件
	 * @param array $screen 传过滤字段
	 * @param mixed $select 传只筛查字段
	 * @param array $loseWhere 传不筛查的字段数组
	 * @param bool $pageCustom 分页的问题
	 */
	public function makeAutoPageList($screen = [], $select = ["*"], $loseWhere = [], $pageCustom = false, $return = 'data', $orderBy = 'id', $func = false, $requestData = []): array
	{
		$this->requestData = $requestData;
		$this->select = $select;
		$this->loseWhere = $loseWhere;
		$q = $this->makeAutoQuery();
		if ($func instanceof Closure) {
			$q->where($func);
		}
		if ($screen) {
			foreach ($screen as $key => $value) {
				if (is_array($value)) {
					//如果是二维数组
					if (count($value) != count($value, 1)) {
						//where[] = ['age'=>[40,70]]
						if (isset($value[1]) && $value[1] == 'in') {
							$q->whereIn($value[0], $value[2]);
						} elseif (isset($value[1]) && $value[1] == 'orLike') {
							$q->where(function ($q2) use ($value) {
								foreach ($value[2] as $v) {
									$q2->orWhere($value[0], 'like', '%' . $v . '%');
								}
							});
						} else {
							$searchKey = array_key_first($value);
							//如果是二维数组需要or判断
							if (is_array($value[$searchKey][0])) {
								//dd($value[$searchKey][1]);
								# code...
								//dd($multi_value);
								$q->where(function ($q2) use ($value, $searchKey) {
									foreach ($value[$searchKey] as $multi_value) {
										if (count($multi_value) == 2) {
											$q2->orWhere(function ($q3) use ($multi_value, $searchKey) {
												$q3->where($searchKey, '>=', $multi_value[0])->where($searchKey, '<=', $multi_value[1]);
											});
										} else if (count($multi_value) == 1) {
											$q2->orWhere($searchKey, $multi_value[0]);
										}
									}
								});
							} else {
								$q->where($searchKey, '>=', $value[$searchKey][0])->where($searchKey, '<=', $value[$searchKey][1]);
							}
						}
						//where[] = ['age','in',[1,2]]
					} else {
						//where[] = ['resulut','like','阳']
						if ($value[1] == 'like') {
							$q->where($value[0], $value[1], '%' . $value[2] . '%');
						} else {
							$q->where($value[0], $value[1], $value[2]);
						}
					}
				} else {
					$q->where($key, $value);
				}
			}
		}
		if (is_array($orderBy)) {
			foreach ($orderBy as $key => $column) {
				$q->orderBy($column[0], $column[1]);
			}
		} else {
			$q->orderBy($orderBy, 'desc');
		}
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
		$forList = [];
		$forList = $list['data'] ?? $list['list'];
		foreach (($forList) as &$list_value) {
			if ($enm_arr && is_array($enm_arr)) {
				foreach ($enm_arr as $k => $value) {
					# code...
					if (isset($list_value[$k])) {
						$list_value[$k . '_str'] = $value[$list_value[$k]] ?? '未定义的枚举类型' . $k . ':' . $list_value[$k];
					}
				}
			}
		}
		unset($list['data']);
		unset($list['list']);
		$list[$return] = $forList;
		return $list;
	}
	public function makeCustomPageList($screen = [], $select = ["*"], $loseWhere = [], $pageCustom = true, $return = 'data', $orderBy = 'id', $func = false, $requestData = []): array
	{
		return $this->makeAutoPageList($screen, $select, $loseWhere, $pageCustom, $return, $orderBy, $func, $requestData);
	}
	/**
	 * 返回总数
	 */
	public function makeCount($screen = [])
	{
		$q = $this->makeAutoQuery();
		if ($screen) {
			foreach ($screen as $key => $value) {
				if (is_array($value)) {
					//如果是二维数组
					if (count($value) != count($value, 1)) {
						//where[] = ['age'=>[40,70]]
						$searchKey = array_key_first($value);
						$q->where($searchKey, '>=', $value[$searchKey][0])->where($searchKey, '<=', $value[$searchKey][1]);
					} else {
						//where[] = ['resulut','like','阳']
						if ($value[1] == 'like') {
							$q->where($value[0], $value[1], '%' . $value[2] . '%');
						} else {
							$q->where($value[0], $value[1], $value[2]);
						}
					}
				} else {
					$q->where($key, $value);
				}
			}
		}
		return $q->count();
	}
	public function makeList($screen = [], $select = ["*"], $loseWhere = [], $pageCustom = true, $return = 'list', $orderBy = 'id', $func = false, $requestData = []): array
	{
		return $this->makeAutoPageList($screen, $select, $loseWhere, $pageCustom, $return, $orderBy, $func, $requestData);
	}
	/**
	 * 自动更新表字段
	 * $query = new Admin();
	 * $res = AutoMake::getQuery($query)->doAutoUpdate();
	 * @param array $onlyUpdate 传只更新字段
	 * @param mixed $except 传不更新的字段
	 * @return bool true/false
	 */
	public function doAutoUpdate($onlyUpdate = ['*'], $except = false): bool
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
		if ($this->query instanceof Builder) {
			$this->table = $table = $this->query->from;
			$q = ($this->query);
		} else {
			$this->table = $table = ($this->query)->getTable();
			$q = ($this->query)->query();
		}
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
