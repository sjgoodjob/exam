<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;
use app\admin\model\User;


class RoomSignupModel extends BaseModel
{


    // 表名
    protected $name = 'exam_room_signup';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];


    public function getStatusList()
    {
        return [
            '0' => '待审核',
            '1' => '报名成功',
            '2' => '报名被拒绝',
        ];
        // return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function room()
    {
        return $this->belongsTo(RoomModel::class, 'room_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
