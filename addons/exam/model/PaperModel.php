<?php

namespace addons\exam\model;


use addons\exam\enum\PaperUses;
use addons\exam\enum\PayAttachType;
use think\Db;

class PaperModel extends \app\admin\model\exam\PaperModel
{
    // 追加属性
    protected $type = [
        'configs' => 'array',
    ];

    public function getCoverImageAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['cover_image']) ? $data['cover_image'] : '');
        return $value ? cdnurl($value, true) : '';
    }

    /**
     * 获取试卷参与人员
     *
     * @param $paper_id
     * @param $slice
     * @return array
     */
    public static function getJoinUsers($paper_id, $slice = 0)
    {
        $user_ids = Db::name('exam_grade')->where('paper_id', $paper_id)->group('user_id')->column('user_id');
        if ($user_ids) {
            // 截取数组
            $user_ids = $slice ? array_slice($user_ids, $slice) : $user_ids;
            return Db::name('user')->whereIn('id', $user_ids)->select();
        }

        return [];
    }

    /**
     * 检查试卷是否需要支付
     * status 0：不能参加，1：可以参加，2：需要支付
     *
     * @param self $paper
     * @param int  $user_id
     * @param bool $from_submit
     * @param int  $room_id
     * @return array
     */
    public static function checkPay($paper, $user_id, $from_submit = false, $room_id = 0)
    {
        $result = [
            'type'         => PayAttachType::PAPER_PAY,
            'status'       => 1, // 0：不能参加，1：可以参加，2：需要支付
            'msg'          => '',
            'is_member'    => UserModel::isMember($user_id),
            'price'        => $paper['price'],
            'member_price' => $paper['member_price'],
            // 'use_type'     => 'paper',// 使用类型：paper=试卷，member=会员
        ];

        // 考场来的暂不收费
        if ($room_id) {
            return $result;
        }

        switch ($paper['uses']) {
            // 针对所有人
            case PaperUses::ALL:
                $cate_id = $paper['cate_id'];
                if (!CateModel::checkUserHasPaperCatePermission($cate_id, $user_id)) {
                    $result['type']   = PayAttachType::OPEN_MEMBER;
                    $result['status'] = 0;
                    $result['msg']    = '该试卷所属分类仅对部分会员开放，请开通后再试';
                    $result['url']    = '/pages/user/member-center';
                    return $result;
                }

                return $result;

            // 仅会员
            case PaperUses::ONLY_MEMBER:
                // 非会员
                if (!$result['is_member']) {
                    $result['type']   = PayAttachType::OPEN_MEMBER;
                    $result['status'] = 0;
                    $result['msg']    = '该试卷仅对会员用户开放';
                    $result['url']    = '/pages/user/member-center';
                    return $result;
                }

                $cate_id = $paper['cate_id'];
                if (!CateModel::checkUserHasPaperCatePermission($cate_id, $user_id)) {
                    $result['type']   = PayAttachType::OPEN_MEMBER;
                    $result['status'] = 0;
                    $result['msg']    = '该试卷所属分类仅对部分会员开放，请开通后再试';
                    $result['url']    = '/pages/user/member-center';
                    return $result;
                }

                // 会员免费
                if (floatval($result['member_price']) == 0) {
                    return $result;
                }

                // 会员须付费但未付费
                if ($from_submit || !PaperOrderModel::hasUsableOrder($paper['id'], $user_id)) {
                    $result['status'] = 2;
                    $result['msg']    = '未支付或支付已过期，请支付后再参加';
                    return $result;
                }
                break;

            // 仅支付用户
            case PaperUses::ONLY_PAY:
                // 非会员
                if (!$result['is_member']) {
                    // 非会员免费
                    if (floatval($result['price']) == 0) {
                        return $result;
                    }

                    // 须付费但未付费
                    if ($from_submit || !PaperOrderModel::hasUsableOrder($paper['id'], $user_id)) {
                        $result['status'] = 2;
                        $result['msg']    = '未支付或支付已过期，请支付后再参加';
                        return $result;
                    }
                } else {
                    // 会员免费
                    if (floatval($result['member_price']) == 0) {
                        return $result;
                    }

                    // 会员须付费但未付费
                    if ($from_submit || !PaperOrderModel::hasUsableOrder($paper['id'], $user_id)) {
                        $result['status'] = 2;
                        $result['msg']    = '未支付或支付已过期，请支付后再参加';
                        return $result;
                    }
                }
                break;
        }

        return $result;
    }
}
