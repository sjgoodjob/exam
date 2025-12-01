<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;


class QuestionCollectModel extends BaseModel
{


    // 表名
    protected $name = 'exam_question_collect';

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
        return $this->belongsTo(\addons\exam\model\QuestionModel::class, 'question_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
