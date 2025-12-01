<?php

namespace addons\exam\model;


class RoomModel extends \app\admin\model\exam\RoomModel
{
    protected $hidden = [
        'password',
    ];

    public function getCoverImageAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['cover_image']) ? $data['cover_image'] : '');
        return $value ? cdnurl($value, true) : '';
    }

    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id');
    }
}
