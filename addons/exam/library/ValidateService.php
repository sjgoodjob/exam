<?php
/**
 * Created by PhpStorm.
 * User         : zgcLives
 * CreateTime   : 2022/6/1 11:56
 */

namespace addons\exam\library;

class ValidateService
{
    /**
     * 验证手机号
     * @param $phone
     * @return false|int
     */
    public static function phone($phone)
    {
        if (!$phone || !\think\Validate::regex($phone, "^1\d{10}$")) {
            return false;
        }
        return true;
    }

    /**
     * 验证身份证号
     * @param $idCardNo
     * @return bool
     */
    public static function idCardNo($idCardNo)
    {
        if (!$idCardNo || !\think\Validate::regex($idCardNo, "/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/")) {
            return false;
        }
        return true;
    }
}
