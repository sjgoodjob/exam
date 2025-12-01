<?php

namespace addons\exam\enum;

use ReflectionClassConstant;

class BaseEnum
{
    /**
     * 获取所有常量
     * @return array
     */
    public static function getConst(): array
    {
        $objClass = new \ReflectionClass(get_called_class());
        return $objClass->getConstants();
    }

    /**
     * 获取所有常量名
     * @return array
     */
    public static function getConstantsKeys(): array
    {
        return array_keys(self::getConst());
    }

    /**
     * 获取所有常量值
     * @return array
     */
    public static function getConstantsValues(): array
    {
        return array_values(self::getConst());
    }

    /**
     * 获取常量注释
     * @param string $key 常量名
     * @return string
     */
    public static function getDescription(string $key): string
    {
        return preg_replace('#[\*\s]*(^/|/$)[\*\s]*#', '', (new ReflectionClassConstant(static::class, $key))->getDocComment());
    }

    /**
     * 获取常量名和注释列表
     * @return array
     */
    public static function getKeyDescription(): array
    {
        $keys   = self::getConstantsKeys();
        $result = [];

        foreach ($keys as $key => $key_name) {
            $result[$key_name] = self::getDescription($key_name);
        }

        return $result;
    }

    /**
     * 获取常量值和注释列表
     * @return array
     */
    public static function getValueDescription(): array
    {
        $const  = self::getConst();
        $result = [];

        foreach ($const as $key => $value) {
            $result[$value] = self::getDescription($key);
        }

        return $result;
    }
}
