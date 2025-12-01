<?php

namespace addons\exam\enum;

/**
 * 支付参数类型
 */
class PayAttachType extends BaseEnum
{
    /** 开通会员 */
    const OPEN_MEMBER = 'OPEN_MEMBER';
    /** 考试支付 */
    const PAPER_PAY = 'PAPER_PAY';
    /** 开通题库 */
    const OPEN_CATE = 'OPEN_CATE';
    /** 课程购买 */
    const COURSE_PAY = 'COURSE_PAY';
}
