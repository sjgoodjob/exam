<?php

namespace addons\exam\enum;

use addons\exam\model\CateOrderModel;
use addons\exam\model\CourseOrderModel;
use addons\exam\model\MemberOrderModel;
use app\admin\model\exam\PaperOrderModel;

/**
 * 支付关联类型
 */
class PayableType extends BaseEnum
{
    /** 开通会员 */
    const MEMBER_ORDER = MemberOrderModel::class;
    /** 考试付费 */
    const PAPER_ORDER = PaperOrderModel::class;
    /** 题库付费 */
    const CATE_ORDER = CateOrderModel::class;
    /** 课程付费 */
    const COURSE_ORDER = CourseOrderModel::class;
}
