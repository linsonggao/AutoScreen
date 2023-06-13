<?php

namespace Lsg\AutoScreen;

abstract class AutoScreenAbstract implements AutoScreenInterface
{
    protected $query;

    protected $page;

    protected $per_page;

    protected $select = ['*'];

    protected $table;

    protected $columnList;

    protected $requestData;

    protected $loseWhere;

    /**
     * 返回model实例
     * @param mixed $query
     * @return object
     */
    public function getQuery($query): object
    {
        $this->query = $query;

        return $this;
    }
}
