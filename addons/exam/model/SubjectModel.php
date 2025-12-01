<?php

namespace addons\exam\model;


class SubjectModel extends \app\admin\model\exam\SubjectModel
{
    protected $append = [];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function child()
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->where('status', '1')
            ->order('weigh desc');
    }
}
