<?php

namespace addons\exam\model;


class CertModel extends \app\admin\model\exam\CertModel
{
    // 追加属性
    protected $append = [
        'status_text',
        'image',
        'create_time_text',
    ];

    public function getImageAttr($value, $data)
    {
        $value = $data['image'] ?? $value;
        return cdnurl($value, true);;
    }

    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['createtime'] ?? '');
        return is_numeric($value) ? date('Y-m-d H:i:s', $value) : $value;
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
