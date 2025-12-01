<?php

namespace addons\exam\model;


use addons\exam\enum\ScoreGoodStatus;

class ScoreGoodModel extends \app\admin\model\exam\ScoreGoodModel
{
    // 追加属性
    protected $append
        = [
            'status_text',
            'createtime_text',
            'updatetime_text',
            // 'images',
            // 'first_image'
        ];

    public function getImagesAttr($value, $data)
    {
        $value  = $data['images'] ?? '';
        $images = explode(',', $value);
        if ($images) {
            foreach ($images as &$image) {
                $image = cdnurl($image, true);
            }
            $value = implode(',', $images);
        }
        return $value;
    }

    public function getFirstImageAttr($value, $data)
    {
        $value = $data['first_image'] ?? '';
        return cdnurl($value, true);
    }

    /**
     * 减库存
     * @param $good
     * @param $quantity
     * @return bool
     */
    public static function decrement($good, $quantity)
    {
        if ($good['stocks'] < 1) {
            return false;
        }
        if ($good['stocks'] - $quantity < 0) {
            return false;
        }

        return self::transaction(function () use ($good, $quantity) {
            $good->stocks -= $quantity;
            // 售罄
            if ($good->stocks == 0) {
                $good->status = ScoreGoodStatus::SELL_OUT;
            }
            return $good->save();
        });
    }
}
