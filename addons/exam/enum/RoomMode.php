<?php

namespace addons\exam\enum;

/**
 * 考场模式
 */
class RoomMode extends BaseEnum
{
    /** 常规模式 */
    const GENERAL = 'GENERAL';
    /** 密码模式 */
    const PASSWORD = 'PASSWORD';
    /** 审核模式 */
    const AUDIT = 'AUDIT';
    // /** 付费 */
    // const PAY = 'PAY';
}
