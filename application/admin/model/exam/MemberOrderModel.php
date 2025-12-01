<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;


class MemberOrderModel extends BaseModel
{
    // 表名
    protected $name = 'exam_member_order';

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
            'status_text',
            'pay_time_text'
        ];


    public function getTypeList()
    {
        return ['VIP_MONTH' => __('Type vip_month'), 'VIP_YEAR' => __('Type vip_year'), 'VIP_LIFE' => __('Type vip_life')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list  = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_time']) ? $data['pay_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setPayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function memberConfig()
    {
        return $this->belongsTo(MemberConfigModel::class, 'member_config_id', 'id', [], 'LEFT')->setEagerlyType(0);
        // return $this->belongsTo(MemberConfigModel::class);
    }
}
