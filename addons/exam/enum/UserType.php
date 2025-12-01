<?php

namespace addons\exam\enum;

/**
 * 会员类型
 */
class UserType extends BaseEnum
{
    /** 普通用户 */
    const NORMAL = 'NORMAL';
    /** 月卡会员 */
    const VIP_MONTH = 'VIP_MONTH';
    /** 年卡会员 */
    const VIP_YEAR = 'VIP_YEAR';
    /** 终身会员 */
    const VIP_LIFE = 'VIP_LIFE';

    /**
     * 获取会员类型时限说明
     * @param $type
     * @return string
     */
    public static function getDurationDesc($type)
    {
        switch ($type) {
            case self::VIP_MONTH:
                return '1个月';

            case self::VIP_YEAR:
                return '12个月';

            case self::VIP_LIFE:
                return '终身会员';

            default:
                return '非会员';
        }
    }

    /**
     * 获取会员类型时限值
     * @param $type
     * @return int
     */
    public static function getDurationVal($type)
    {
        switch ($type) {
            case self::VIP_MONTH:
                return intval(date("t", strtotime(date('Y-m-d'))));

            case self::VIP_YEAR:
                $year = date('Y');
                $days = 0;
                for ($month = 1; $month <= 12; $month++) {
                    $days = $days + date("t", strtotime("{$year}-{$month}"));
                }
                return $days;

            case self::VIP_LIFE:
                return 365 * 100;

            default:
                return 0;
        }
    }
}
