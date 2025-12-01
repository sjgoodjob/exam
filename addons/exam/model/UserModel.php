<?php

namespace addons\exam\model;

use addons\exam\enum\UserStatus;
use app\common\library\Auth;
use fast\Random;

class UserModel extends BaseModel
{
    // 表名
    protected $name = 'user';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // /**
    //  * 登录并返回token
    //  * @param $username
    //  * @param $password
    //  * @param $user_type
    //  * @return string
    //  */
    // public static function login($username, $password, $user_type)
    // {
    //     $username = "{$user_type}-$username";
    //     if (!$user = self::where('username', $username)->find()) {
    //         api_fail('登录账号或密码错误');
    //     }
    //     if ($user['password'] != Auth::instance()->getEncryptPassword($password, $user['salt'])) {
    //         api_fail('登录账号或密码错误');
    //     }
    //
    //     Auth::instance()->direct($user['id']);
    //     return Auth::instance()->getToken();
    // }

    // /**
    //  * 当前登录用户信息
    //  * @return Auth|null
    //  */
    // public static function info()
    // {
    //     if (Auth::instance()->isLogin()) {
    //         return Auth::instance();
    //     }
    //     return null;
    // }

    /**
     * 快速注册用户
     *
     * @param string $username
     * @param string $nickname
     * @param string $avatar
     * @param int    $gender
     * @param string $password
     * @param string $mobile
     * @return UserModel
     */
    public static function fastRegister(string $username, string $nickname = '', string $avatar = '', int $gender = 0, string $password = '', string $mobile = '')
    {
        if (self::where('username', $username)->count()) {
            exam_fail('该账号已被注册');
        }
        // 不严格要求可以去除
        // if ($mobile && self::where('mobile', $mobile)->count()) {
        //     fail('该手机号码已被绑定');
        // }

        $salt = Random::alnum();
        return self::create([
            'username'  => $username,
            'mobile'    => $mobile,
            'email'     => $mobile . '@qq.com',
            'salt'      => $salt,
            'password'  => Auth::instance()->getEncryptPassword($password ?: $username, $salt),
            'nickname'  => $nickname,
            'avatar'    => $avatar,
            'gender'    => $gender,
            'status'    => UserStatus::NORMAL,
            'logintime' => time(),
        ]);
    }

    /**
     * 获取用户扩展信息
     *
     * @param $user_id
     * @return UserInfoModel
     */
    public static function getInfo($user_id)
    {
        return UserInfoModel::getUserInfo($user_id);
    }

    /**
     * 用户是否是会员
     *
     * @param $user_id
     * @return bool
     */
    public static function isMember($user_id)
    {
        $info = UserInfoModel::getUserInfo($user_id);
        return $info['status'] == 1;
        // return $info['type'] != UserType::NORMAL && $info['expire_time'] > time();
    }

    /**
     * 成为会员
     *
     * @param int $user_id
     * @param int $days
     * @param int $member_config_id
     * @return bool
     */
    public static function beMember($user_id, int $days, int $member_config_id = 0)
    {
        // $now = time();
        // if (!$expire_time) {
        //     switch ($type) {
        //         case UserType::VIP_MONTH:
        //             $expire_time = $now + (30 * 86400);
        //             break;
        //         case UserType::VIP_YEAR:
        //             $expire_time = $now + (365 * 86400);
        //             break;
        //         case UserType::VIP_LIFE:
        //             $expire_time = $now + (365 * 86400 * 100);
        //             break;
        //     }
        // }

        $user_info                     = UserModel::getInfo($user_id);
        $user_info['expire_time']      = time() + ($days * 86400);
        $user_info['member_config_id'] = $member_config_id;
        return $user_info->save();
    }

    /**
     * 获取用户微信openid（登录时存username了）
     *
     * @param $user_id
     * @return string
     */
    public static function getOpenId($user_id)
    {
        $user = UserModel::get($user_id);
        return $user['username'];
    }

    /**
     * 是否必须绑定手机号
     *
     * @param $user
     * @return void
     */
    public static function isMustBindMobile($user)
    {
        $system_config = exam_getConfig('system_config');
        $bind_mobile   = $system_config['bind_mobile'] ?? 0;
        if ($bind_mobile == 2 && (!isset($user['mobile']) || !$user['mobile'])) {
            exam_fail('请先绑定手机号');
        }
    }

    /************************** 关联关系 **************************/

    public function info()
    {
        return $this->hasOne(UserInfoModel::class, 'user_id', 'id');
    }
}
