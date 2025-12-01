<?php

namespace addons\exam\model;


class SlideModel extends \app\admin\model\exam\SlideModel
{
    protected $type = [
        'front_info' => 'array',
    ];

    public function getImageAttr($value, $data)
    {
        $image = $value ?: $data['image'] ?? '';
        return $image ? cdnurl($image, true) : '';
    }
}
