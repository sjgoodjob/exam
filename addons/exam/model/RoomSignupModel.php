<?php

namespace addons\exam\model;


class RoomSignupModel extends \app\admin\model\exam\RoomSignupModel
{
    public function room()
    {
        return $this->belongsTo(RoomModel::class, 'room_id', 'id');
    }
}
