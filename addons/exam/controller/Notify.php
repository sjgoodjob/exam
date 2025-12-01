<?php

namespace addons\exam\controller;

use addons\exam\enum\PayStatus;
use addons\exam\library\PaymentCallbackService;
use addons\exam\library\WechatService;
use app\admin\model\exam\PayLogModel;

class Notify extends Base
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 微信支付回调
     */
    public function pay()
    {
        $app      = WechatService::getPayment();
        $response = $app->handlePaidNotify(function ($message, $fail) {
            // 测试回调日志
            // @file_put_contents('test_notify.txt',
            //     json_encode(
            //         [
            //             'message' => $message,
            //             'fail'    => $fail,
            //         ], JSON_UNESCAPED_UNICODE
            //     ) . PHP_EOL, FILE_APPEND);

            // 支付记录
            $log = PayLogModel::create([
                'openid'         => $message['openid'],
                'mch_id'         => $message['mch_id'],
                'out_trade_no'   => $message['out_trade_no'],
                'pay_money'      => $message['total_fee'] / 100,
                'transaction_id' => $message['transaction_id'],
                'app_id'         => $message['appid'],
                'status'         => PayStatus::PAID,
                'pay_time'       => time(),
                'response'       => json_encode($message),
            ]);

            try {
                if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                    // 用户是否支付成功
                    if ($message['result_code'] === 'SUCCESS') {
                        // 统一处理订单回调
                        $order = PaymentCallbackService::handleOrder($message);

                        // 支付订单相关信息
                        $log->user_id      = $order->user_id;
                        $log->payable_id   = $order->id;
                        $log->payable_type = get_class($order);
                        $log->save();

                    } else {
                        // 用户支付失败
                        $this->handleError($log, 'result_code FAIL');
                    }
                } else {
                    return $fail('通信失败，请稍后再通知我');
                }
            } catch (\Exception $ex) {
                $this->handleError($log, $ex->getMessage());
            }

            return true;
        });

        $response->send();
    }

    /**
     * 记录回调错误信息
     * @param $log
     * @param $error_message
     * @return void
     */
    protected function handleError($log, $error_message)
    {
        $log->error_message = $error_message;
        $log->error_time    = time();
        $log->save();

        // 记录错误信息
        // @file_put_contents('test_notify.txt',
        //     json_encode(
        //         [
        //             'message' => $error_message,
        //         ]
        //     ) . PHP_EOL, FILE_APPEND);
    }
}
