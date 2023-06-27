<?php
declare(strict_types = 1);

namespace Lsg\AutoScreen\Enum;

/**
 * 商品类型
 */
enum ProductType: int
{
    use ExtendEnum;
    #[Description('商品')]
    case PRODUCT = 1;
    #[Description('服务')]
    case SERVICE = 2;
    #[Description('消耗品')]
    case CONSUMPTION = 3;
}

ProductType::PRODUCT->name; // PRODUCT
ProductType::PRODUCT->value; // 1
ProductType::PRODUCT->getDescription(); // 商品
ProductType::keyValues(); // [1 => '商品', 2 => '服务', 3 => '消耗品']
