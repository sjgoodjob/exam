<?php

namespace addons\exam\enum;

/**
 * 积分类型
 */
class UserScoreType extends BaseEnum
{
    /** 每日登录 */
    const LOGIN = 'LOGIN';
    /** 参加刷题 */
    const LOOK = 'LOOK';
    /** 参加练习 */
    const TRAIN = 'TRAIN';
    /** 参加试卷考试 */
    const PAPER = 'PAPER';
    /** 参加考场考试 */
    const ROOM = 'ROOM';
    /** 后台手动操作 */
    const MANUAL = 'MANUAL';
    /** 积分兑换 */
    const EXCHANGE = 'EXCHANGE';
}
