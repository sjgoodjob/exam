<?php

namespace addons\exam\controller;

use addons\exam\enum\ScoreGoodOrderStatus;
use addons\exam\enum\ScoreGoodStatus;
use addons\exam\model\ScoreGoodModel;
use addons\exam\model\ScoreGoodOrderModel;
use addons\exam\model\UserModel;
use addons\exam\model\UserScoreLogModel;

/**
 * 积分接口
 */
class Score extends Base
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    /**
     * 积分记录
     */
    public function logs()
    {
        $list = UserScoreLogModel::where('user_id', $this->auth->id)->order('id desc')->paginate();
        $this->success('', ['list' => $list]);
    }

    /**
     * 积分商品列表
     */
    public function goods()
    {
        $this->success('', ScoreGoodModel::where('status', '>', 0)->paginate(10));
    }

    /**
     * 积分商品详情
     */
    public function goodDetail()
    {
        if (!$id = input('id/d', 0)) {
            $this->error('缺少商品ID');
        }
        if (!$good = ScoreGoodModel::get($id)) {
            $this->error('商品信息不存在');
        }
        if ($good['status'] != ScoreGoodStatus::NORMAL) {
            $this->error('商品已下架或已售罄');
        }

        // 上次的收货信息
        $order         = ScoreGoodOrderModel::where('user_id', $this->auth->id)->order('id', 'desc')->find();
        $receiver_info = [
            'user_name' => $order['user_name'] ?? '',
            'phone'     => $order['phone'] ?? '',
            'address'   => $order['address'] ?? '',
        ];

        $this->success('', [
            'good'          => $good,
            'receiver_info' => $receiver_info,
        ]);
    }

    /**
     * 积分兑换
     */
    public function exchange()
    {
        // $user = $this->auth->getUser();
        $user_info = UserModel::getInfo($this->auth->id);

        switch (true) {
            case !$id = input('id/d'):
                exam_fail('缺少商品参数');
            case !$quantity = input('quantity/d'):
                exam_fail('请填写兑换数量');
            case !$user_name = input('user_name/s'):
                exam_fail('请填写收货人');
            case !$phone = input('phone/s'):
                exam_fail('请填写收货人手机');
            case !$address = input('address/s'):
                exam_fail('请填写收货人地址');

            case !$good = ScoreGoodModel::where('id', $id)->where('status', ScoreGoodStatus::NORMAL)->find():
                exam_fail('积分商品不存在或已下架');
            case $good->stocks < $quantity:
                exam_fail('当前积分商品库存不足');
            case $good->limit < $quantity || $good->limit < $quantity + ScoreGoodOrderModel::where('user_id', $this->auth->id)
                    ->where('good_id', $id)
                    ->count():
                exam_fail('您的兑换次数超过该商品限购数量');
            case $good->price * $quantity > $user_info['score']:
                exam_fail('您的积分不足');
        }

        // 兑换
        $exchange = ScoreGoodOrderModel::exchange($this->auth->id, $good, $quantity, $user_name, $phone, $address);

        $this->success('兑换成功', [
            'exchange' => $exchange,
        ]);
    }

    /**
     * 积分兑换订单列表
     */
    public function orders()
    {
        $query = ScoreGoodOrderModel::where('user_id', $this->auth->id);

        $status = input('status/d');
        if (is_numeric($status) && in_array($status, ScoreGoodOrderStatus::getConstantsValues())) {
            $query->where('status', $status);
        }

        $status_list = [
            [
                'name'   => '全部',
                'status' => -1,
                // 'list'   => [],
            ],
        ];
        foreach (ScoreGoodOrderStatus::getValueDescription() as $status => $text) {
            $status_list[] = [
                'name'   => $text,
                'status' => $status,
            ];
        }

        $this->success('', [
            'status_list' => $status_list,
            'list'        => $query->order('id', 'desc')->paginate(10),
        ]);
    }

    /**
     * 积分兑换订单详情
     */
    public function orderDetail()
    {
        if (!$id = input('id/d', 0)) {
            $this->error('缺少订单ID');
        }
        if (!$order = ScoreGoodOrderModel::get($id)) {
            $this->error('订单信息不存在');
        }

        $status_list = [];
        foreach (ScoreGoodOrderStatus::getValueDescription() as $status => $text) {
            $status_list[] = [
                'name'   => $text,
                'status' => $status,
            ];
        }
        $this->success('', [
            'status_list' => $status_list,
            'order'       => $order,
        ]);
    }

    /**
     * 订单完成
     */
    public function complete()
    {
        if (!$id = input('id/d', 0)) {
            $this->error('缺少订单ID');
        }
        if (!$order = ScoreGoodOrderModel::where('id', $id)->where('user_id', $this->auth->id)->find()) {
            $this->error('订单信息不存在');
        }
        if ($order['status'] != ScoreGoodOrderStatus::SHIP) {
            $this->error('订单未发货，无法进行完成操作');
        }

        $order['status']        = ScoreGoodOrderStatus::COMPLETE;
        $order['complete_time'] = time();
        if ($order->save()) {
            $this->success('订单完成成功');
        }

        $this->error('完成操作失败，请重试');
    }
}
