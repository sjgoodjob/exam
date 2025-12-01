<?php

namespace app\admin\model\exam;

use addons\exam\enum\ScoreGoodStatus;
use addons\exam\model\BaseModel;
use traits\model\SoftDelete;

class ScoreGoodModel extends BaseModel
{

    use SoftDelete;


    // 表名
    protected $name = 'exam_score_good';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append
        = [
            'status_text',
            'createtime_text',
            'updatetime_text',
        ];


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });

        self::beforeWrite(function ($row) {
            // 记录商品首图
            if ($row['images']) {
                $row->first_image = explode(',', $row['images'])[0];
            }
        }, true);
    }


    public function getStatusList()
    {
        return ScoreGoodStatus::getValueDescription();
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['createtime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getUpdatetimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['updatetime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


}
