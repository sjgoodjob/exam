<?php

namespace addons\exam\controller;

use addons\exam\enum\GeneralStatus;
use addons\exam\enum\PayAttachType;
use addons\exam\enum\PayStatus;
use addons\exam\library\WechatService;
use addons\exam\model\MemberOrderModel;
use addons\exam\model\UserInfoModel;
use addons\exam\model\UserModel;
use app\admin\model\exam\MemberCodeModel;
use app\admin\model\exam\MemberConfigModel;
use app\common\library\Token;
use think\Db;
use think\Validate;
use think\Lang;
use fast\Random;

/**
 * 用户接口
 */
class User extends Base
{
    protected $noNeedLogin = ['login', 'userLogin', 'getWechatPhone', 'register'];
    protected $noNeedRight = '*';
    protected $visibleFields = ['id', 'avatar', 'gender', 'nickname', 'mobile', 'birthday', 'status', 'createtime', 'logintime'];

    /**
     * 授权登录
     * @ApiMethod   (POST)
     *
     * @param string $code     授权code
     * @param string $userInfo 授权后拿到的用户信息
     */
    public function login()
    {
        $user_info    = input('userInfo/a', []);
        $code         = input('code/s', '');
        $from_user_id = input('from_user_id/d', 0);
        $mobile       = input('mobile/s', '');

        if (!$code) {
            exam_fail('缺少小程序参数code');
        }
        if (!$user_info) {
            exam_fail('缺少小程序参数userInfo');
        }

        $service     = new WechatService();
        $wechat_user = $service->miniLogin($code);
        if (!isset($wechat_user['openid'])) {
            exam_fail('获取小程序用户信息失败');
        }

        $open_id     = $wechat_user['openid'];
        $session_key = $wechat_user['session_key'] ?? '';

        $user = UserModel::get(['username' => $open_id]);
        if (empty($user)) {
            $nickname = $user_info['nickName'] ?? '';
            if (!$nickname || $nickname == '微信用户') {
                $nickname = '用户' . Random::alnum();
            }

            $user = UserModel::fastRegister($open_id, $nickname, $user_info['avatarUrl'] ?? '', $user_info['gender'] ?? 0, '', $mobile);
            if (!$user) {
                exam_fail('注册用户失败');
            }
        } else {
            $data = [
                // 'nickname'  => $user_info['nickName'],
                // 'avatar'    => $user_info['avatarUrl'],
                'logintime' => time(),
            ];

            if ($mobile) {
                $data['mobile'] = $mobile;
            }

            // if (!$user->parent_id) {
            //     $data['parent_id'] = $from_user_id;
            // }

            $user->isUpdate(true)->save($data);
        }

        // 记录session_key，用于后续获取手机号码等功能
        // CacheService::setWechatUserSessionKey($user->id, $session_key);

        // 清除之前的token
        Token::clear($user->id);

        // 直接登录
        $this->auth->direct($user->id);

        // 用户扩展信息
        $info = UserModel::getInfo($user->id);

        $this->success('', [
            'token' => $this->auth->getToken(),
            'user'  => array_merge($user->only($this->visibleFields), ['info' => $info->toArray()]),
        ]);
    }

    /**
     * 用户信息
     */
    public function info()
    {
        $user         = $this->auth->getUser()->visible($this->visibleFields)->toArray();
        $user['info'] = UserInfoModel::getUserInfo($this->auth->id);
        if ($user && $user['avatar']) {
            if (strpos($user['avatar'], 'http') === false) {
                $user['avatar'] = cdnurl($user['avatar'], true);
            }
        }
        $this->success('', $user);
    }

    /**
     * 获取微信绑定的手机号码
     */
    // public function getWechatPhone()
    // {
    //     $iv            = input('iv/s', '');
    //     $encryptedData = input('encryptedData/s', '');
    //
    //     if (!$iv) {
    //         fail('缺少小程序参数iv');
    //     }
    //     if (!$encryptedData) {
    //         fail('缺少小程序参数encryptedData');
    //     }
    //     if (!$session_key = CacheService::getWechatUserSessionKey($this->auth->id)) {
    //         fail('微信sessionKey丢失，请重新登录再试');
    //     }
    //
    //     // dd($session_key, $iv, $encryptedData);
    //     // try {
    //     $service = new WechatService();
    //     $data    = $service->decryptedData($session_key, $iv, $encryptedData);
    //     succ($data);
    //     // } catch (\Exception $exception) {
    //     //     fail('sessionKey失效，请重新登录再试：' . $exception->getMessage());
    //     // }
    // }

    /**
     * 保存个人信息
     */
    public function save()
    {
        $update_fields = ['avatar', 'nickname', 'mobile', 'gender', 'birthday'];
        $data          = ['updatetime' => time()];
        foreach ($update_fields as $field) {
            $value = input("{$field}/s", '');
            if ($value !== '') {
                $data[$field] = $value;
            }
        }

        $user = $this->auth->getUser();
        if ($user->save($data)) {
            exam_succ(['user' => $user->visible($this->visibleFields)]);
        }

        exam_fail('保存失败，请重试');
    }

    /**
     * 获取会员配置
     */
    public function memberConfigs()
    {
        $this->success('', exam_getConfig('member_config'));
    }

    /**
     * 获取会员开通配置列表
     */
    public function memberOpenConfig()
    {
        $list = MemberConfigModel::where('status', GeneralStatus::NORMAL)->order('price')->select();
        $list = $list ? collection($list)->toArray() : [];
        foreach ($list as &$item) {
            $item['cate_names']       = MemberConfigModel::getCateNames($item);
            $item['paper_cate_names'] = MemberConfigModel::getPaperCateNames($item);
        }
        $this->success('', $list);
    }

    /**
     * 开通会员
     */
    public function createMemberOrder()
    {
        if (!$member_config_id = input('member_config_id/d', '')) {
            $this->error('请选择要开通的会员类型');
        }
        if (!$member_config = MemberConfigModel::get($member_config_id)) {
            $this->error('会员类型配置不存在');
        }
        if ($member_config['status'] != GeneralStatus::NORMAL) {
            $this->error('此会员类型暂不开放开通');
        }
        if (UserModel::isMember($this->auth->id)) {
            $this->error('您已经是会员了，无须重复开通');
        }

        // 开通费用
        // $open_fee = $config["member_{$type_lower}_fee"];
        $order = MemberOrderModel::create([
            'user_id'          => $this->auth->id,
            'order_no'         => exam_generate_no('M'),
            'member_config_id' => $member_config_id,
            'days'             => $member_config['days'],
            'amount'           => $member_config['price'],
            'status'           => $member_config['price'] > 0 ? PayStatus::UNPAID : PayStatus::PAID,
        ]);

        // 无须支付
        if (!$member_config['price'] && UserModel::beMember($this->auth->id, $member_config['days'])) {
            $this->success('', [
                'type' => 'beMember',
            ]);
        }

        // 支付参数
        $service = new WechatService();
        $payment = $service->unifyPay($this->auth->username, $order['order_no'], intval(bcmul($member_config['price'], 100, 2)), '开通' . $member_config['name'], PayAttachType::OPEN_MEMBER);

        $this->success('', [
            'type'    => 'orderPay',
            'order'   => $order,
            'payment' => $payment,
        ]);
    }

    /**
     * 激活会员
     */
    public function activateMember()
    {
        if (!$code = input('code/s', '')) {
            $this->error('请输入会员激活码');
        }
        // if (CacheService::getActivateMemberCount($this->auth->id) > 3) {
        //     $this->error('激活操作太频繁了，稍后再试');
        // }
        if (UserModel::isMember($this->auth->id)) {
            $this->error('您已经是会员了，无须重复激活');
        }
        if (!$memberCode = MemberCodeModel::get(['code' => $code])) {
            $this->error('会员激活码无效');
        }
        if ($memberCode['status'] == 1) {
            $this->error('会员激活码已失效');
        }
        $memberConfig = $memberCode->member_config;
        if (!$memberConfig || !$memberConfig['status']) {
            $this->error('会员激活码配置异常，请联系客服');
        }

        $result = Db::transaction(function () use ($memberCode, $memberConfig) {
            // 成为会员
            UserModel::beMember($this->auth->id, $memberConfig['days'], $memberConfig['id']);

            // 记录激活信息
            $memberCode->status        = 1;
            $memberCode->user_id       = $this->auth->id;
            $memberCode->activate_time = time();
            return $memberCode->save();
        });

        if ($result) {
            exam_succ(['info' => UserModel::getInfo($this->auth->id)]);
        }

        $this->error('操作失败，请重试');
    }

    /**
     * 账号密码注册
     */
    public function register()
    {
        if (!$username = input('username/s')) {
            exam_fail('请填写登录账号');
        }
        if (!$password = input('password/s')) {
            exam_fail('请填写登录密码');
        }
        if (!$nickname = input('nickname/s')) {
            exam_fail('请填写昵称');
        }
        if (!$mobile = input('mobile/s')) {
            exam_fail('请填写手机号码');
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            exam_fail(__('Mobile is incorrect'));
        }
        $gender = input('gender/d', 1);

        // 注册
        $user = UserModel::fastRegister($username, $nickname, '', $gender, $password, $mobile);
        // 用户扩展信息
        $info = UserInfoModel::getUserInfo($user->id);
        // 接口层登录
        $this->auth->direct($user->id);
        exam_succ([
            'user'  => array_merge($user->only($this->visibleFields), ['info' => $info->toArray()]),
            'token' => $this->auth->getToken(),
        ]);
    }

    /**
     * 账号密码登录
     */
    public function userLogin()
    {
        if (!$username = input('username/s')) {
            exam_fail('请填写登录账号');
        }
        if (!$password = input('password/s')) {
            exam_fail('请填写登录密码');
        }

        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }

        $user = UserModel::get(['username' => $username]);
        if (!$user) {
            exam_fail('登录失败，账号或密码错误');
        }
        if ($user->password != $this->auth->getEncryptPassword($password, $user->salt)) {
            exam_fail('登录失败，账号或密码错误.');
        }
        if ($user->status != 'normal') {
            exam_fail('登录失败，账号已被禁用登录');
        }

        // 清除之前的token
        // Token::clear($user->id);
        // 用户扩展信息
        $info = UserInfoModel::getUserInfo($user->id);
        // 接口层登录
        $this->auth->direct($user->id);
        exam_succ([
            'user'  => array_merge($user->only($this->visibleFields), ['info' => $info->toArray()]),
            'token' => $this->auth->getToken(),
        ]);
    }

    /**
     * 手机号码快速获取
     */
    public function getWechatPhone()
    {
        $code = input('code/s', '');
        if (!$code) {
            exam_fail('缺少小程序参数code');
        }
        $type = input('type/s', '');
        $kind = input('kind/s', '');

        $service = new WechatService();
        $phone   = $service->getMobile($code);

        // 登录或注册
        if ($type == 'regOrLogin') {
            $user     = UserModel::get(['username' => $phone]);
            $operate  = 'login';
            $password = '';
            if (!$user) {
                // 随机生成6位密码
                $password = strtolower(\fast\Random::alnum(6));
                $user     = UserModel::fastRegister($phone, $phone, '', 0, $password, $phone);
                $operate  = 'register';
                // if ($kind == 'register') {
                //     $user    = UserModel::fastRegister($phone, $phone, '', 0, '123456', $phone);
                //     $operate = 'register';
                // } else {
                //     fail('账号不存在，请先注册');
                // }
            }

            $this->auth->direct($user->id);
            // 用户扩展信息
            $user['info'] = UserInfoModel::getUserInfo($user->id);

            exam_succ([
                'token'    => $this->auth->getToken(),
                'user'     => $user->only($this->visibleFields),
                'mobile'   => $phone,
                'password' => $password,
                'operate'  => $operate,
            ]);
        }

        exam_succ([
            'mobile' => $phone,
        ]);
    }

    /**
     * 修改密码
     */
    public function changePassword()
    {
        $old_password = input('oldPassword/s', '');
        $new_password = input('newPassword/s', '');

        if (!$old_password) {
            exam_fail('请输入旧密码');
        }
        if (!$new_password) {
            exam_fail('请输入新密码');
        }

        Lang::load(APP_PATH . 'index' . DS . 'lang' . DS . 'zh-cn' . DS . 'user.php');
        $rule = [
            'oldpassword' => 'require|regex:\S{6,30}',
            'newpassword' => 'require|regex:\S{6,30}',
        ];

        $msg      = [
            // 'renewpassword.confirm' => __('Password and confirm password don\'t match'),
        ];
        $data     = [
            'oldpassword' => $old_password,
            'newpassword' => $new_password,
        ];
        $field    = [
            'oldpassword' => '旧密码',
            'newpassword' => '新密码',
        ];
        $validate = new Validate($rule, $msg, $field);
        $result   = $validate->check($data);
        if (!$result) {
            $this->error(__($validate->getError()));
        }

        $ret = $this->auth->changepwd($new_password, $old_password);
        if ($ret) {
            $this->success('修改成功', [
                'token' => $this->auth->getToken(),
            ]);
        } else {
            $this->error($this->auth->getError());
        }
    }
}
