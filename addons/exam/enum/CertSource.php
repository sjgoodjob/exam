<?php

namespace addons\exam\enum;

/**
 * 证书来源
 */
class CertSource extends BaseEnum
{
    /** 手动 */
    const MANUAL = 'manual';
    /** 试卷 */
    const PAPER = 'paper';
    /** 考场 */
    const ROOM = 'room';
}
