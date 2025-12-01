<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;


class QuestionWrongModel extends BaseModel
{
    // 表名
    protected $name = 'exam_question_wrong';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'user_answer',
    ];

    public function getUserAnswerAttr($value, $data)
    {
        $user_answer = json_decode($data['user_answer'], true);
        if (!$user_answer) {
            if (is_string($data['user_answer'])) {
                return $data['user_answer'];
            }
        }
        return $user_answer;
    }

    public function question()
    {
        return $this->belongsTo(\addons\exam\model\QuestionModel::class, 'question_id');
        // return $this->belongsTo(\addons\exam\model\QuestionModel::class, 'question_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id');
    }

    public function room()
    {
        return $this->belongsTo(RoomModel::class, 'room_id');
    }
}
