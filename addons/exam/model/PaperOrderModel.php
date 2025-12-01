<?php

namespace addons\exam\model;


use addons\exam\enum\PaperPayStatus;
use addons\exam\enum\PayAttachType;
use addons\exam\library\WechatService;

class PaperOrderModel extends \app\admin\model\exam\PaperOrderModel
{
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id', 'id');
    }


    /**
     * 用户是否有已支付且可用的订单
     *
     * @param $paper_id
     * @param $user_id
     * @return PaperOrderModel
     */
    public static function hasUsableOrder($paper_id, $user_id)
    {
        $time = time();
        return self::where('user_id', $user_id)
            ->where('paper_id', $paper_id)
            ->where('status', PaperPayStatus::PAID)
            ->whereRaw("expire_time = 0 OR (expire_time > 0 AND expire_time > {$time})")
            ->order('id')
            ->find();
    }

    /**
     * 创建订单
     *
     * @param int    $paper_id
     * @param int    $user_id
     * @param double $amount
     * @param int    $expire_time
     * @return PaperOrderModel
     */
    public static function createOrder($paper_id, $user_id, $amount, $expire_time = 0)
    {
        $order_no = exam_generate_no('P');
        return self::create([
            'paper_id'    => $paper_id,
            'user_id'     => $user_id,
            'order_no'    => $order_no,
            'amount'      => $amount,
            'status'      => $amount > 0 ? 0 : 1,
            'expire_time' => $expire_time,
        ]);
    }

    /**
     * 创建支付参数
     *
     * @param $user_id
     * @param $order_no
     * @param $price
     * @return array
     */
    public static function createPayment($user_id, $order_no, $price)
    {
        $open_id = UserModel::getOpenId($user_id);
        // 支付参数
        $service = new WechatService();
        return $service->unifyPay($open_id, $order_no, $price * 100, '考试付费', PayAttachType::PAPER_PAY);
    }

    /**
     * 设置订单为已使用
     *
     * @param $order_no
     * @param $user_id
     * @return PaperOrderModel
     */
    public static function setOrderUsed($order_no, $user_id)
    {
        return self::where('order_no', $order_no)->where('user_id', $user_id)->update(['status' => PaperPayStatus::USED]);
    }

    // public static function setOrderUsed($paper_id, $user_id)
    // {
    //
    // }
}
