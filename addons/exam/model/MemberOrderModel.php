<?php

namespace addons\exam\model;


use app\admin\model\exam\MemberConfigModel;

class MemberOrderModel extends \app\admin\model\exam\MemberOrderModel
{
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function memberConfig()
    {
        return $this->belongsTo(MemberConfigModel::class, 'member_config_id', 'id');
    }
}
