<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;
use traits\model\SoftDelete;


class RoomModel extends BaseModel
{
    use SoftDelete;
    
    // 表名
    protected $name = 'exam_room';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'start_time_text',
        'end_time_text',
        'status_text',
        'signup_mode_text',
        'is_makeup_text'
    ];


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function getStatusList()
    {
        return ['NORMAL' => __('Normal'), 'HIDDEN' => __('Hidden')];
    }

    public function getSignupModeList()
    {
        return ['NORMAL' => __('Signup_mode normal'), 'PASSWORD' => __('Signup_mode password'), 'AUDIT' => __('Signup_mode audit')];
    }

    public function getIsMakeupList()
    {
        return ['0' => __('Is_makeup 0'), '1' => __('Is_makeup 1')];
    }


    public function getStartTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['start_time']) ? $data['start_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['end_time']) ? $data['end_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSignupModeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['signup_mode']) ? $data['signup_mode'] : '');
        $list  = $this->getSignupModeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsMakeupTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_makeup']) ? $data['is_makeup'] : '');
        $list  = $this->getIsMakeupList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setStartTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cate()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cates()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id');
    }


}
