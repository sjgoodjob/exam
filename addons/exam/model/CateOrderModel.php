<?php

namespace addons\exam\model;


use app\admin\model\exam\CateModel;

class CateOrderModel extends \app\admin\model\exam\CateOrderModel
{
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function cate()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id');
    }

}
