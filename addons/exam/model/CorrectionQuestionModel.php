<?php

namespace addons\exam\model;


class CorrectionQuestionModel extends \app\admin\model\exam\CorrectionQuestionModel
{
    // 追加属性
    protected $append = [
        'createtime_text',
        'status_text'
    ];

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2')];
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(QuestionModel::class, 'question_id', 'id');
    }
}
