<?php
/**
 * Created by PhpStorm.
 * User         : zgcLives
 * CreateTime   : 2022/7/15 15:54
 */

namespace addons\exam\library;

use addons\exam\enum\CateOpenType;
use addons\exam\enum\PaperPayStatus;
use addons\exam\enum\PayAttachType;
use addons\exam\enum\PayStatus;
use addons\exam\model\CateOrderModel;
use addons\exam\model\CateUserLogModel;
use addons\exam\model\CourseOrderModel;
use addons\exam\model\MemberOrderModel;
use addons\exam\model\PaperOrderModel;
use addons\exam\model\UserModel;

/**
 * 支付回调服务
 */
class PaymentCallbackService
{
    /**
     * 统一处理订单回调
     *
     * @param array $response
     * @return MemberOrderModel|void
     */
    public static function handleOrder(array $response)
    {
        // 转换金额单位
        $response['pay_money'] = $response['total_fee'] / 100;

        switch ($response['attach']) {
            // 会员开通
            case PayAttachType::OPEN_MEMBER:
                return self::handleMemberOrder($response);

            // 考试支付
            case PayAttachType::PAPER_PAY:
                return self::handlePaperPayOrder($response);

            // 题库开通
            case PayAttachType::OPEN_CATE:
                return self::handleCateOrder($response);

            // 课程支付
            case PayAttachType::COURSE_PAY:
                return self::handleCourseOrder($response);

            default:
                exam_fail("未知的订单attach值：{$response['attach']}");
        }

    }

    /**
     * 处理开通会员订单回调
     *
     * @param $response
     * @return MemberOrderModel
     */
    public static function handleMemberOrder($response)
    {
        if (!$order = MemberOrderModel::where('order_no', $response['out_trade_no'])->find()) {
            exam_fail("订单编号不存在：{$response['out_trade_no']}");
        }
        if ($order->status != PayStatus::UNPAID) {
            exam_fail("订单已支付：{$response['out_trade_no']}");
        }
        if ($order->amount != $response['pay_money']) {
            exam_fail("订单金额不匹配：{$response['out_trade_no']}");
        }

        // 记录订单支付信息
        $order->status    = PayStatus::PAID;
        $order->pay_money = $response['pay_money'];
        $order->pay_time  = time();
        $order->save();

        // 成为会员
        UserModel::beMember($order['user_id'], $order['days'], $order['member_config_id']);

        return $order;
    }

    /**
     * 处理考试支付订单回调
     *
     * @param $response
     * @return PaperOrderModel
     */
    public static function handlePaperPayOrder($response)
    {
        if (!$order = PaperOrderModel::where('order_no', $response['out_trade_no'])->find()) {
            exam_fail("订单编号不存在：{$response['out_trade_no']}");
        }
        if ($order->status != PaperPayStatus::UNPAID) {
            exam_fail("订单已支付：{$response['out_trade_no']}");
        }
        if ($order->amount != $response['pay_money']) {
            exam_fail("订单金额不匹配：{$response['out_trade_no']}");
        }

        // 记录订单支付信息
        $order->status    = PaperPayStatus::PAID;
        $order->pay_money = $response['pay_money'];
        $order->pay_time  = time();
        $order->save();

        // TODO 后续可在此做统计

        return $order;
    }

    /**
     * 处理考试支付订单回调
     *
     * @param $response
     * @return CateUserLogModel
     */
    public static function handleCateOrder($response)
    {
        if (!$order = CateOrderModel::where('order_no', $response['out_trade_no'])->find()) {
            exam_fail("订单编号不存在：{$response['out_trade_no']}");
        }
        if ($order->status != PaperPayStatus::UNPAID) {
            exam_fail("订单已支付：{$response['out_trade_no']}");
        }
        if ($order->amount != $response['pay_money']) {
            exam_fail("订单金额不匹配：{$response['out_trade_no']}");
        }

        // 记录订单支付信息
        $order->status    = PaperPayStatus::PAID;
        $order->pay_money = $response['pay_money'];
        $order->pay_time  = time();
        $order->save();

        $expire_time = 0;
        if ($order['days'] > 0) {
            $expire_time = time() + $order['days'] * 24 * 3600;
        }

        // 记录题库开通
        CateUserLogModel::create([
            'user_id'     => $order['user_id'],
            'cate_id'     => $order['cate_id'],
            'type'        => CateOpenType::PAY,
            'expire_time' => $expire_time,
        ]);

        return $order;
    }

    /**
     * 处理课程支付订单回调
     *
     * @param $response
     * @return CourseOrderModel
     */
    public static function handleCourseOrder($response)
    {
        if (!$order = CourseOrderModel::where('order_no', $response['out_trade_no'])->find()) {
            exam_fail("订单编号不存在：{$response['out_trade_no']}");
        }
        if ($order->status != PaperPayStatus::UNPAID) {
            exam_fail("订单已支付：{$response['out_trade_no']}");
        }
        if ($order->amount != $response['pay_money']) {
            exam_fail("订单金额不匹配：{$response['out_trade_no']}");
        }

        // 记录订单支付信息
        $order->status    = PaperPayStatus::PAID;
        $order->pay_money = $response['pay_money'];
        $order->pay_time  = time();
        $order->channel   = 'WECHAT';
        $order->save();

        $expire_time = 0;
        if ($order['days'] > 0) {
            $expire_time = time() + $order['days'] * 24 * 3600;
        }

        // 记录题库开通
        CateUserLogModel::create([
            'user_id'     => $order['user_id'],
            'cate_id'     => $order['cate_id'],
            'type'        => CateOpenType::PAY,
            'expire_time' => $expire_time,
        ]);

        return $order;
    }
}
