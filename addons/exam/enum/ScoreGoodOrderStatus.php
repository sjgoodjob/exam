<?php

namespace addons\exam\enum;

/**
 * 积分兑换订单状态
 */
class ScoreGoodOrderStatus extends BaseEnum
{
    /** 未支付 */
    const UNPAID = 0;
    /** 已支付 */
    const PAID = 10;
    /** 已发货 */
    const SHIP = 20;
    /** 已完成 */
    const COMPLETE = 30;
}
