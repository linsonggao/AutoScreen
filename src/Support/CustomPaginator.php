<?php

namespace Lsg\AutoScreen\Support;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 自定义分页器
 */
class CustomPaginator extends LengthAwarePaginator
{
    /**
     * 最大每页数量
     */
    public const MAX_PER_PAGE = 99999999;

    /**
     * 标识是否取回所有数据
     *
     * @var bool
     */
    protected $fetchAll = false;

    /**
     * @param LengthAwarePaginator $paginate 原始分页对象
     * @param bool $fetchAll 标识是否取回所有数据
     *
     * @return void
     */
    public function __construct(LengthAwarePaginator $paginate, bool $fetchAll = false)
    {
        parent::__construct(
            items: $paginate->getCollection(),
            total: $paginate->total(),
            perPage: $paginate->perPage(),
            currentPage: $paginate->currentPage()
        );

        $this->fetchAll = $fetchAll;
    }

    /**
     * **重写方法**
     *
     * 重新定义分页的输出格式
     *
     * @return array
     */
    public function toArray(): array
    {
        if ($this->fetchAll) {
            return ['list' => $this->getCollection()];
        }

        return [
            'paginate' => [
                'total'        => (int) $this->total(),
                'current_page' => (int) $this->currentPage(),
                'page_size'    => (int) $this->perPage(),
            ],
            'list'     => $this->getCollection(),
        ];
    }

    /**
     * 空数据的分页对象
     *
     * @param bool $fetchAll 标识是否取回所有数据
     *
     * @return self
     */
    public static function toEmpty(bool $fetchAll = false): self
    {
        return new self(
            new LengthAwarePaginator(
                items: collect(),
                total: 0,
                perPage: (int) request()->input('per_page', 15)
            ),
            $fetchAll
        );
    }
}
