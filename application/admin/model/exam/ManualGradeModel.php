<?php

namespace app\admin\model\exam;

use think\Model;


class ManualGradeModel extends Model
{

    

    

    // 表名
    protected $name = 'exam_manual_grade_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'kind_text',
        'status_text'
    ];
    

    
    public function getKindList()
    {
        return ['PAPER' => __('Paper'), 'ROOM' => __('Room')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getKindTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['kind']) ? $data['kind'] : '');
        $list = $this->getKindList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function admin()
    {
        return $this->belongsTo('app\admin\model\Admin', 'id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function paper()
    {
        return $this->belongsTo('Paper', 'paper_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function question()
    {
        return $this->belongsTo('Question', 'question_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
