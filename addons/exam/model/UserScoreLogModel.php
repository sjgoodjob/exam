<?php

namespace addons\exam\model;

class UserScoreLogModel extends \app\admin\model\exam\UserScoreLogModel
{
    // 隐藏属性
    protected $hidden = ['changeable_id', 'changeable_type'];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
