<?php

namespace addons\exam\enum;

/**
 * 支付状态
 */
class PayStatus extends BaseEnum
{
    /** 未支付 */
    const UNPAID = 0;
    /** 已支付 */
    const PAID = 1;
}
