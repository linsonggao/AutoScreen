<?php

namespace Lsg\AutoScreen\Support;

use App\Models\Admin;

/**
 * 管理员会话
 */
class GlobalParams
{
    /**
     * 指定会话验证器
     */
    protected static $validatorRule = null;

    /**
     * 指定会话验证器属性
     */
    protected static $validatorAttr = null;

    /**
     * 指定会话验证器属性
     *
     * @param Admin $admin
     * @return void
     */
    public static function getValidatorAttr()
    {
        return self::$validatorAttr ?: null;
    }

    /**
     * 指定会话验证器属性
     *
     * @param Admin $admin
     * @param mixed $validatorRule
     * @param mixed $validatorAtt
     * @param mixed $validatorAttr
     * @return void
     */
    public static function setValidatorAttr($validatorAttr): void
    {
        self::$validatorAttr = $validatorAttr;
    }

    /**
     * 指定会话验证器
     *
     * @param Admin $admin
     * @return void
     */
    public static function getValidatorRule()
    {
        return self::$validatorRule ?: null;
    }

    /**
     * 指定会话验证器
     *
     * @param Admin $admin
     * @param mixed $validatorRule
     * @return void
     */
    public static function setValidatorRule($validatorRule): void
    {
        self::$validatorRule = $validatorRule;
    }
}
