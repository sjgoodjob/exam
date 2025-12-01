<?php

namespace addons\exam\model;

use addons\exam\enum\UserType;

class UserInfoModel extends \app\admin\model\exam\UserInfoModel
{

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    /**
     * 初始化用户扩展信息
     * @param $user_id
     * @param $type
     * @return UserInfoModel
     */
    public static function initInfo($user_id, $type = UserType::NORMAL)
    {
        return self::create([
            'user_id'          => $user_id,
            'type'             => $type,
            'score'            => 0,
            'expire_time'      => 0,
            'member_config_id' => 0,
        ]);
    }

    /**
     * 获取用户扩展信息
     * @param $user_id
     * @return UserInfoModel
     */
    public static function getUserInfo($user_id)
    {
        if ($info = self::where('user_id', $user_id)->find()) {
            return $info;
        }

        return self::initInfo($user_id);
    }
}
