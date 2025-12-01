<?php

namespace addons\exam\model;

use addons\exam\enum\ScoreGoodOrderStatus;
use addons\exam\enum\UserScoreType;
use think\Db;

class ScoreGoodOrderModel extends \app\admin\model\exam\ScoreGoodOrderModel
{
    protected $type = [
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function good()
    {
        return $this->belongsTo(ScoreGoodModel::class, 'good_id', 'id');
    }

    /**
     * 兑换积分商品
     *
     * @param int            $user_id
     * @param ScoreGoodModel $good
     * @param int            $quantity
     * @param string         $user_name
     * @param string         $phone
     * @param string         $address
     * @return mixed|void
     */
    public static function exchange($user_id, ScoreGoodModel $good, int $quantity, string $user_name, string $phone, string $address)
    {
        try {
            return Db::transaction(function () use ($user_id, $good, $quantity, $user_name, $phone, $address) {
                // 兑换记录
                $exchange = self::create([
                    'order_no'    => exam_generate_no('SG'),
                    'user_id'     => $user_id,
                    'user_name'   => $user_name,
                    'phone'       => $phone,
                    'address'     => $address,
                    'good_id'     => $good->id,
                    'quantity'    => $quantity,
                    'name'        => $good->getData('name'),
                    'first_image' => $good->first_image,
                    'price'       => $good->price,
                    'amount'      => $good->price * $quantity,
                    'status'      => ScoreGoodOrderStatus::PAID,
                    'pay_time'    => time(),
                ]);

                // 减积分
                UserScoreLogModel::decrement($user_id, $exchange->amount, UserScoreType::EXCHANGE, $exchange, "兑换商品");

                // 减库存
                ScoreGoodModel::decrement($good, $quantity);
                // $good->setDec('stocks', $quantity);

                return $exchange;
            });
        } catch (\Exception $exception) {
            exam_fail('兑换失败，系统异常：' . $exception->getMessage());
        }
    }
}
