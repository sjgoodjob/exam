<?php

namespace app\admin\model\exam;

use think\Model;


class CateUserLogModel extends Model
{


    // 表名
    protected $name = 'exam_cate_user_log';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append
        = [
            'type_text',
            'expire_time_text'
        ];


    public function getTypeList()
    {
        return ['PAY' => __('Type pay'), 'CODE' => __('Type code')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list  = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getExpireTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['expire_time']) ? $data['expire_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setExpireTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function cate()
    {
        return $this->belongsTo('Cate', 'cate_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 判断用户是否开通了某个分类
     * @param $user_id
     * @param $cate_id
     * @return bool
     */
    public static function isOpenCate($user_id, $cate_id)
    {
        return CateUserLogModel::where('user_id', $user_id)
                ->where('cate_id', $cate_id)
                ->where(function ($query) {
                    $query->where('expire_time', '>', time())
                        ->whereOr('expire_time', 0);
                })
                ->count() > 0;
    }
}
