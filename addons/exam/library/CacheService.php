<?php
/**
 * Created by PhpStorm.
 * User         : zgcLives
 * CreateTime   : 2022/10/20 11:57
 */

namespace addons\exam\library;

use think\Cache;

/**
 * 缓存服务
 */
class CacheService
{
    /**
     * 缓存Key：微信用户sessionKey
     * @param $user_id
     * @return string
     */
    public static function cacheKeyWechatUserSessionKey($user_id)
    {
        return "exam:wechat_user:session_key-{$user_id}";
    }

    /**
     * 设置微信用户sessionKey缓存
     * @param $user_id
     * @param $session_key
     * @return void
     */
    public static function setWechatUserSessionKey($user_id, $session_key)
    {
        $cache_key = self::cacheKeyWechatUserSessionKey($user_id);
        cache($cache_key, $session_key);
    }

    /**
     * 获取微信用户sessionKey缓存
     * @param $user_id
     * @return string
     */
    public static function getWechatUserSessionKey($user_id)
    {
        $cache_key = self::cacheKeyWechatUserSessionKey($user_id);
        return cache($cache_key);
    }

    /**
     * 缓存Key：激活码激活次数
     * @param $user_id
     * @return string
     */
    public static function cacheKeyActivateMemberCountKey($user_id)
    {
        return "exam:activate_member_count:{$user_id}-" . date('Y-m-d');
    }

    /**
     * 设置微信用户sessionKey缓存
     * @param $user_id
     * @param $session_key
     * @return void
     */
    public static function setActivateMemberCount($user_id)
    {
        $cache_key = self::cacheKeyWechatUserSessionKey($user_id);
        if (Cache::get($cache_key)) {
            Cache::inc($cache_key, 1);
        } else {
            Cache::set($cache_key, 1, 300);
        }
    }

    /**
     * 获取微信用户sessionKey缓存
     * @param $user_id
     * @return string
     */
    public static function getActivateMemberCount($user_id)
    {
        $cache_key = self::cacheKeyWechatUserSessionKey($user_id);
        $count     = cache($cache_key);
        return $count ?? 0;
    }
}
