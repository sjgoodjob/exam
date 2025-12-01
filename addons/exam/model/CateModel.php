<?php

namespace addons\exam\model;


use addons\exam\enum\PayAttachType;

class CateModel extends \app\admin\model\exam\CateModel
{
    /**
     * 检测题库是否需要付费
     *
     * @param $cate_id
     * @param $user_id
     * @return array
     */
    public static function checkPay($cate_id, $user_id)
    {
        if (!$cate = CateModel::get($cate_id)) {
            exam_fail('题库信息不存在');
        }

        $result = [
            'type'   => PayAttachType::OPEN_CATE,
            'status' => 1, // 1：可以参加，2：需要支付
            'msg'    => '',
            'price'  => $cate['price'],
            'cate'   => $cate,
        ];

        if (!$cate['is_free'] && $cate['price'] > 0) {
            if (!CateUserLogModel::isOpenCate($user_id, $cate['id'])) {
                // fail('该题库需要付费开通，请先购买后再试', ['need_open' => true, 'cate' => $cate]);
                $result['status'] = 2;
                $result['msg']    = "该题库需要付费或激活码开通";//【{$cate['name']}】
            }
        }

        // 仅限会员
        if ($cate['uses'] == 'ONLY_MEMBER') {
            CateModel::checkUserHasCatePermission($cate_id, $user_id);
            // if (!UserModel::isMember($user_id)) {
            //     $result['status'] = 2;
            //     $result['msg']    = "该题库仅限会员使用";
            // }
        }

        return $result;
    }
}
