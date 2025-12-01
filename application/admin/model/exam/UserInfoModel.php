<?php

namespace app\admin\model\exam;

use addons\exam\enum\UserType;
use addons\exam\model\BaseModel;
use think\Db;


class UserInfoModel extends BaseModel
{
    // 表名
    protected $name = 'exam_user_info';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append
        = [
            'type_text',
            'expire_time_text',
            'status',
        ];


    public function getTypeList()
    {
        // return ['NORMAL' => __('Type normal'), 'VIP_MONTH' => __('Type vip_month'), 'VIP_YEAR' => __('Type vip_year'), 'VIP_LIFE' => __('Type vip_life')];
        return UserType::getKeyDescription();
    }


    public function getTypeTextAttr($value, $data)
    {
        // $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        // $list  = $this->getTypeList();
        // return isset($list[$value]) ? $list[$value] : '';

        $member_config_id = isset($data['member_config_id']) ? $data['member_config_id'] : 0;
        if ($member_config_id) {
            // 会员或曾经是会员
            if ($member_name = Db::name('exam_member_config')->where('id', $member_config_id)->value('name')) {
                return $member_name;
            }
        }
        return '普通用户';
    }


    public function getExpireTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['expire_time']) ? $data['expire_time'] : '');
        return is_numeric($value) && $value ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setExpireTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function getStatusAttr($value, $data)
    {
        // 非会员
        $status = 0;
        // $member_config_id = isset($data['member_config_id']) ? $data['member_config_id'] : 0;
        $expire_time = isset($data['expire_time']) ? $data['expire_time'] : 0;
        if ($expire_time > 0) {//$member_config_id &&
            // 是会员
            $status = 1;
        }
        if ($expire_time < time()) {
            // 会员过期
            $status = 2;
        }

        return $status;
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function memberConfig()
    {
        return $this->belongsTo(MemberConfigModel::class, 'member_config_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 隐藏手机号码
     * @param $mobile
     * @return string
     */
    public static function hideUserMobile($mobile)
    {
        if (!$mobile) {
            return '';
        }
        $mobile = (string)$mobile;
        $len    = strlen($mobile);
        if ($len == 11) {
            return substr($mobile, 0, 3) . '****' . substr($mobile, 7, 4);
        } else {
            return $mobile;
        }
    }
}
