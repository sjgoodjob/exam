<?php

namespace addons\exam\model;


class QuestionCollectModel extends \app\admin\model\exam\QuestionCollectModel
{
    public function question()
    {
        return $this->belongsTo(QuestionModel::class, 'question_id', 'id');
    }
}
