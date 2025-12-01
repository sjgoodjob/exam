<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;
use app\admin\model\User;


class GradeModel extends BaseModel
{
    // 表名
    protected $name = 'exam_grade';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        // 'grade_time_text'
    ];


    public function getGradeTimeTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['grade_time'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setGradeTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cate()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
