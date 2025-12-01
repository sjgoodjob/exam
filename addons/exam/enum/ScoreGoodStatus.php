<?php

namespace addons\exam\enum;

/**
 * 积分商品状态
 */
class ScoreGoodStatus extends BaseEnum
{
    /** 下架 */
    const DOWN = 0;
    /** 上架 */
    const NORMAL = 10;
    /** 售罄 */
    const SELL_OUT = 20;
}
