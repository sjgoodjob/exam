<?php

namespace app\admin\model\exam;

use addons\exam\enum\UserScoreKind;
use addons\exam\enum\UserScoreType;
use addons\exam\model\BaseModel;
use addons\exam\model\UserModel;
use think\Db;


class UserScoreLogModel extends BaseModel
{
    // 表名
    protected $name = 'exam_user_score_log';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append
        = [
            'type_text',
            'createtime_text',
        ];

    public function getTypeList(): array
    {
        return UserScoreType::getKeyDescription();
    }

    public function getTypeTextAttr($value, $data): string
    {
        $value = $value ?: ($data['type'] ?? '');
        $list  = $this->getTypeList();
        return $list[$value] ?? '';
    }

    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['createtime'] ?? '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    /************************** 关联关系 **************************/

    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 积分变动多态关联模型。
     */
    public function changeable()
    {
        return $this->morphTo();
    }

    /************************** 积分操作 **************************/

    /**
     * 积分增加
     *
     * @param int    $user_id     用户ID
     * @param int    $score       变动积分
     * @param string $type        变动类型
     * @param null   $changeModel 关联模型
     * @return false|mixed
     */
    public static function increment(int $user_id, int $score, string $type, $changeModel = null, $memo = '')
    {
        if ($score <= 0) {
            return false;
        }

        // 获取用户扩展信息
        $info = UserModel::getInfo($user_id);

        return Db::transaction(function () use ($info, $user_id, $score, $type, $changeModel, $memo) {
            // 变动记录
            self::create([
                'user_id'         => $user_id,
                'kind'            => UserScoreKind::INC,
                'type'            => $type,
                'score'           => $score,
                'before'          => $info->score,
                'after'           => $info->score + $score,
                'changeable_id'   => $changeModel ? $changeModel->id : $info->id,
                'changeable_type' => $changeModel ? get_class($changeModel) : get_class($info),
                'date'            => date('Y-m-d', time()),
                'memo'            => $memo,
            ]);

            $info->setInc('score', $score);
            $info->setInc('score_inc', $score); // 累计获得积分

            return $info->score;
        });

    }

    /**
     * 积分减少
     *
     * @param int    $user_id     用户ID
     * @param int    $score       变动积分
     * @param string $type        变动类型
     * @param null   $changeModel 关联模型
     * @return false|mixed
     */
    public static function decrement(int $user_id, int $score, string $type, $changeModel = null, $memo = '')
    {
        if ($score <= 0) {
            return false;
        }

        // 获取用户扩展信息
        $info = UserModel::getInfo($user_id);

        // 积分
        if ($score > $info->score) {
            exam_fail('用户积分不足');
        }

        return Db::transaction(function () use ($info, $user_id, $score, $type, $changeModel, $memo) {
            // 变动记录
            self::create([
                'user_id'         => $user_id,
                'kind'            => UserScoreKind::DEC,
                'type'            => $type,
                'score'           => $score,
                'before'          => $info->score,
                'after'           => $info->score - $score,
                'changeable_id'   => $changeModel ? $changeModel->id : $info->id,
                'changeable_type' => $changeModel ? get_class($changeModel) : get_class($info),
                'date'            => date('Y-m-d', time()),
                'memo'            => $memo,
            ]);

            $info->setDec('score', $score);
            $info->setInc('score_dec', $score); // 累计支出积分

            return $info->score;
        });
    }

}
