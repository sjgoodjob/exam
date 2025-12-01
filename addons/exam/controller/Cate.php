<?php

namespace addons\exam\controller;

use addons\exam\enum\CateKind;
use addons\exam\enum\CateOpenType;
use addons\exam\enum\PayAttachType;
use addons\exam\enum\PayStatus;
use addons\exam\library\WechatService;
use addons\exam\model\CateCodeModel;
use addons\exam\model\CateModel;
use addons\exam\model\CateOrderModel;
use addons\exam\model\CateUserLogModel;
use think\Db;

/**
 * 分类接口
 */
class Cate extends Base
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 按种类查询分类
     */
    public function index()
    {
        if (!$kind = input('kind/s')) {
            exam_fail('缺少分类种类参数');
        }

        $model = new CateModel();

        $data['level1'] = $model->where('status', '1')->where('kind', $kind)->where('level', 1)->order('sort desc')->select();
        $data['level2'] = $model->where('status', '1')->where('kind', $kind)->where('level', 2)->order('sort desc')->select();
        $data['level3'] = $model->where('status', '1')->where('kind', $kind)->where('level', 3)->order('sort desc')->select();

        $this->success('', $data);
    }

    /**
     * 3级分类筛选
     */
    public function filter()
    {
        $kind       = input('kind/s');
        $subject_id = input('subject_id/d', 0);

        if (!$kind || !in_array($kind, [CateKind::PAPER, CateKind::QUESTION, CateKind::ROOM])) {
            exam_fail('筛选参数错误');
        }

        $submenu = [];
        switch ($kind) {
            case CateKind::QUESTION:
            case CateKind::PAPER:
                $submenu = [
                    [
                        'name'  => '默认排序',
                        'value' => '',
                    ],
                    [
                        'name'  => '按参与人数从低到高',
                        'value' => "join_count|asc",
                    ],
                    [
                        'name'  => '按参与人数从高到低',
                        'value' => "join_count|desc",
                    ],
                ];
                break;
            case CateKind::ROOM:
                $submenu = [
                    [
                        'name'  => '默认排序',
                        'value' => '',
                    ],
                    [
                        'name'  => '按报考人数从低到高',
                        'value' => "signup_count|asc",
                    ],
                    [
                        'name'  => '按报考人数从高到低',
                        'value' => "signup_count|desc",
                    ],
                    [
                        'name'  => '按考试人数从低到高',
                        'value' => "grade_count|asc",
                    ],
                    [
                        'name'  => '按考试人数从高到低',
                        'value' => "grade_count|desc",
                    ],
                    [
                        'name'  => '按及格人数从低到高',
                        'value' => "pass_count|asc",
                    ],
                    [
                        'name'  => '按及格人数从高到低',
                        'value' => "pass_count|desc",
                    ],
                ];
                break;
        }

        $filter = [
            [
                'name'    => '筛选分类',
                'type'    => 'hierarchy',
                'submenu' => CateModel::threeLevel($kind, $subject_id),
            ],
            [
                'name'    => '排序',
                'type'    => 'hierarchy',
                'submenu' => $submenu,
            ],
        ];

        $this->success('', $filter);
    }

    /**
     * 3级分类获取
     */
    public function getThree()
    {
        $kind       = input('kind/s');
        $type       = input('type/s', '');
        $subject_id = input('subject_id/d', 0);

        if (!$kind || !in_array($kind, [CateKind::PAPER, CateKind::QUESTION, CateKind::ROOM, CateKind::COURSE, CateKind::SIGN_UP])) {
            exam_fail('筛选参数错误');
        }

        $cates = CateModel::threeLevel2($kind, $type, $subject_id);
        $this->success('', $cates);
    }

    /**
     * 检测是否已开通题库
     */
    public function checkPay()
    {
        if (!$cate_id = input('cate_id/d', '')) {
            $this->error('请选择题库');
        }

        $result = CateModel::checkPay($cate_id, $this->auth->id);
        $this->success('', $result);
    }

    /**
     * 开通题库
     */
    public function createOrder()
    {
        if (!$cate_id = input('cate_id/d', '')) {
            $this->error('请选择要开通的题库');
        }
        if (!$cate = CateModel::get($cate_id)) {
            $this->error('题库不存在');
        }
        if ($cate['is_free']) {
            $this->error('此题库暂免费开放，无须付费开通');
        }
        // if ($cate['status'] != GeneralStatus::NORMAL) {
        //     $this->error('此题库暂不开放开通');
        // }
        if (CateUserLogModel::isOpenCate($this->auth->id, $cate_id)) {
            $this->error('该题库您已开通，无须重复开通');
        }

        // 开通费用
        $order = CateOrderModel::create([
            'user_id'  => $this->auth->id,
            'order_no' => exam_generate_no('C'),
            'cate_id'  => $cate_id,
            'amount'   => $cate['price'],
            'status'   => $cate['price'] > 0 ? PayStatus::UNPAID : PayStatus::PAID,
            'days'     => $cate['days'],
        ]);

        // 无须支付
        if (!$cate['price']) {
            $this->success('', [
                'type' => 'openCate',
            ]);
        }

        // 支付参数
        $service = new WechatService();
        $payment = $service->unifyPay($this->auth->username, $order['order_no'], $cate['price'] * 100, '开通题库', PayAttachType::OPEN_CATE);

        $this->success('', [
            'type'    => 'orderPay',
            'order'   => $order,
            'payment' => $payment,
        ]);
    }

    /**
     * 激活题库
     */
    public function activateCate()
    {
        if (!$cate_id = input('cate_id/s', '')) {
            $this->error('请选择题库');
        }
        if (!$code = input('code/s', '')) {
            $this->error('请输入题库激活码');
        }
        if (!$cateCode = CateCodeModel::get(['code' => $code], 'cate')) {
            $this->error('题库激活码无效');
        }
        if ($cateCode['status'] == 1) {
            $this->error('题库激活码已失效');
        }
        // if ($cateCode['cate_id'] != $cate_id) {
        //     $this->error('激活码与题库不匹配');
        // }
        // if (!$cateCode['cate']) {
        //     $this->error('激活码对应题库不存在，请联系管理员');
        // }
        if (!$cateCode['cate_id']) {
            $this->error('激活码未关联任何题库');
        }
        $cate_ids = explode(',', trim($cateCode['cate_id']));
        if (!in_array($cate_id, $cate_ids)) {
            $this->error('激活码与题库不匹配');
        }
        if (CateUserLogModel::isOpenCate($this->auth->id, $cateCode['cate_id'])) {
            $this->error('该题库您已开通，无须重复激活');
        }

        $result = Db::transaction(function () use ($cateCode, $cate_ids) {
            $expire_time = 0;
            if ($cateCode['days'] > 0) {
                // 有效期
                $expire_time = strtotime("+{$cateCode['days']} days");
            }

            // 多题库激活
            foreach ($cate_ids as $cate_id) {
                // 记录题库开通
                CateUserLogModel::create([
                    'user_id'     => $this->auth->id,
                    'cate_id'     => $cate_id, //$cateCode['cate_id'],
                    'type'        => CateOpenType::CODE,
                    'expire_time' => $expire_time,
                ]);
            }

            // 记录激活信息
            $cateCode->status        = 1;
            $cateCode->user_id       = $this->auth->id;
            $cateCode->activate_time = time();
            return $cateCode->save();
        });

        if ($result) {
            exam_succ(['cate' => $cateCode['cate']]);
        }

        $this->error('操作失败，请重试');
    }
}
