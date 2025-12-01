<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;


class PaperQuestionModel extends BaseModel
{
    // use SoftDelete;

    // 表名
    protected $name = 'exam_paper_question';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [];

    protected $type = [
        // 'answer' => 'array'
    ];

    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(QuestionModel::class, 'question_id', 'id');
    }
}
