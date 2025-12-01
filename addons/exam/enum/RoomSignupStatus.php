<?php

namespace addons\exam\enum;

/**
 * 考场报名状态
 */
class RoomSignupStatus extends BaseEnum
{
    /** 待审核 */
    const WAIT = 0;
    /** 报名成功 */
    const ACCEPT = 1;
    /** 报名被拒绝 */
    const REJECT = 2;
}
