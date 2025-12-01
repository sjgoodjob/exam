<?php

namespace addons\exam\library;


use addons\exam\model\UserModel;
use app\admin\model\exam\UserScoreLogModel;

/**
 * 积分服务
 */
class ScoreService
{
    /**
     * 积分配置
     *
     * @var array
     */
    protected static $config = [];

    /**
     * 获取积分配置
     *
     * @return array|mixed|string|null
     */
    public static function getScoreConfig()
    {
        if (!self::$config) {
            self::$config = exam_getConfig('score_config');
        }
        if (!self::$config) {
            return [];
            // fail('未配置积分参数');
        }
        return self::$config;
    }

    /**
     * 获取某种类型的得积分值
     *
     * @param string $type
     * @return int
     */
    public static function getScoreVal(string $type): int
    {
        $type   = strtolower($type);
        $config = self::getScoreConfig();
        return $config["score_val_{$type}"] ?? 0;
    }

    /**
     * 获取某种类型的得积分上限
     *
     * @param string $type
     * @return int
     */
    public static function getScoreCount(string $type): int
    {
        $type   = strtolower($type);
        $config = self::getScoreConfig();
        return $config["score_count_{$type}"];
    }

    /**
     * 检查今日是否已超出获得积分上限
     *
     * @param int    $user_id
     * @param string $type
     * @return bool
     */
    public static function checkLimit($user_id, string $type): bool
    {
        $limit = self::getScoreCount($type);
        $limit = $limit ?: 0;

        return UserScoreLogModel::where('date', date('Y-m-d'))->where('user_id', $user_id)->where('type', $type)->count() < $limit;
    }

    /**
     * 获取某种类型的积分
     *
     * @param int    $user_id
     * @param string $type
     * @param null   $changeModel
     * @param string $memo
     * @return int
     */
    public static function getScore($user_id, string $type, $changeModel = null, string $memo = ''): int
    {
        switch (true) {
            // 未登录
            case !$user_id:
                // 未设置得积分
            case !$score = self::getScoreVal($type):
                // 非会员
            case !UserModel::isMember($user_id):
                // 超过得分限制
            case !self::checkLimit($user_id, $type):
                return 0;
        }

        // 递增用户积分并记录
        UserScoreLogModel::increment($user_id, $score, $type, $changeModel, $memo);
        return $score;
    }

}
