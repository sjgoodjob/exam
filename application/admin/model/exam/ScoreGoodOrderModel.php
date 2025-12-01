<?php

namespace app\admin\model\exam;

use addons\exam\enum\ScoreGoodOrderStatus;
use addons\exam\model\BaseModel;


class ScoreGoodOrderModel extends BaseModel
{
    // 表名
    protected $name = 'exam_score_good_order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append
        = [
            'status_text',
            'pay_time_text',
            'ship_time_text',
            'complete_time_text',
            'createtime_text',
            'updatetime_text',
        ];


    public function getStatusList()
    {
        return ScoreGoodOrderStatus::getValueDescription();
        // return ['0' => __('Status 0'), '10' => __('Status 10'), '20' => __('Status 20'), '30' => __('Status 30')];
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


    public function getShipTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['ship_time']) ? $data['ship_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getCompleteTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['complete_time']) ? $data['complete_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['createtime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getUpdatetimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['updatetime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setPayTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setShipTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setCompleteTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function good()
    {
        return $this->belongsTo(ScoreGoodModel::class, 'good_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
