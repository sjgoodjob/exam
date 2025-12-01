<?php

namespace app\admin\model\exam;

use app\admin\model\User;
use think\helper\Str;
use think\Model;


class MemberCodeModel extends Model
{


    // 表名
    protected $name = 'exam_member_code';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append
        = [
            'status_text'
        ];


    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function config()
    {
        return $this->belongsTo(MemberConfigModel::class, 'member_config_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function memberConfig()
    {
        return $this->belongsTo(MemberConfigModel::class, 'member_config_id');
    }

    /**
     * 根据时间生成编号
     * @return string
     */
    public static function randomStr($length = '')
    {
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return Str::substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 生成码
     * "@"代表任意随机英文字符，"#"代表任意随机数字，"*"代表任意英文或数字
     * 规则样本：@@@@##@@#@##****
     * @return string
     */
    public static function generateCode()
    {
        return strtoupper(
            self::randomStr(4) .
            mt_rand(10, 99) .
            self::randomStr(2) .
            mt_rand(0, 9) .
            self::randomStr(1) .
            mt_rand(10, 99) .
            Str::random(4)
        );
    }

    /**
     * 生成码 - 简易方式
     * @param int $length 长度
     * @return string
     */
    public static function generateSimpleCode($length = 8)
    {
        return mt_rand(1 . str_repeat(0, $length - 1), str_repeat(9, $length));
    }
}
