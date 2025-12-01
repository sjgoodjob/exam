<?php

namespace app\admin\model\exam;

use think\Model;


class NewsModel extends Model
{
    // 表名
    protected $name = 'exam_news';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
    ];


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk          = $row->getPk();
            $images      = $row->images ? explode(',', $row->images) : [];
            $cover_image = $images[0] ?? '';

            $row->getQuery()->where($pk, $row[$pk])->update([
                'weigh'       => $row[$pk],
                'cover_image' => $cover_image,
            ]);
        });
    }


    public function getStatusList()
    {
        return ['NORMAL' => __('Normal'), 'HIDDEN' => __('Hidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


}
