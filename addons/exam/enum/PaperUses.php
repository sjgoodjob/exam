<?php

namespace addons\exam\enum;

/**
 * 试卷可用群体
 */
class PaperUses extends BaseEnum
{
    /** 所有用户免费 */
    const ALL = 'ALL';
    /** 会员专用 */
    const ONLY_MEMBER = 'ONLY_MEMBER';
    /** 须支付 */
    const ONLY_PAY = 'ONLY_PAY';
}
