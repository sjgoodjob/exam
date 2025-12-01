<?php

namespace app\admin\model\exam;

use think\Model;


class MaterialQuestionModel extends Model
{
    // 表名
    protected $name = 'exam_material_question';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];


    public function question()
    {
        return $this->belongsTo('QuestionModel', 'question_id', 'id');
        // return $this->belongsTo('Question', 'question_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function parentQuestion()
    {
        return $this->belongsTo('QuestionModel', 'parent_question_id', 'id');
    }
}
