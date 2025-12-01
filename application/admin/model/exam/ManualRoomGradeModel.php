<?php

namespace app\admin\model\exam;

use think\Model;


class ManualRoomGradeModel extends Model
{

    

    

    // 表名
    protected $name = 'exam_manual_room_grade_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function admin()
    {
        return $this->belongsTo('app\admin\model\Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function paper()
    {
        return $this->belongsTo('Paper', 'id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function room()
    {
        return $this->belongsTo('Room', 'room_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function question()
    {
        return $this->belongsTo('Question', 'question_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function grade()
    {
        return $this->belongsTo('Grade', 'grade_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
