<?php

namespace addons\exam\enum;

/**
 * 考试支付状态
 */
class PaperPayStatus extends BaseEnum
{
    /** 未支付 */
    const UNPAID = 0;
    /** 已支付未使用 */
    const PAID = 1;
    /** 已使用 */
    const USED = 2;
}
