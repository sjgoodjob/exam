<?php

namespace addons\exam\model;


class NewsModel extends \app\admin\model\exam\NewsModel
{
    protected $type = [
        'front_info' => 'array',
    ];

    // 追加属性
    protected $append = [
        'status_text',
        'images',
        'cover_image',
        'create_time_text',
    ];

    public function getImagesAttr($value, $data)
    {
        $value  = $data['images'] ?? $value;
        $images = explode(',', $value);
        if ($images) {
            foreach ($images as &$image) {
                $image = cdnurl($image, true);
            }
            $value = implode(',', $images);
        }
        return $value;
    }

    public function getCoverImageAttr($value, $data)
    {
        $images = $data['images'] && is_string($data['images']) ? explode(',', $data['images']) : [];
        $image  = $value ?: $images[0] ?? '';
        return $image ? cdnurl($image, true) : '';
    }

    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['createtime'] ?? '');
        return is_numeric($value) ? date('Y-m-d H:i:s', $value) : $value;
    }
}
