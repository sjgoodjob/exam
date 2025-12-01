<?php

namespace addons\exam\controller;

use addons\exam\model\RoomSignupModel;


/**
 * 考场接口
 */
class RoomSignup extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];
    protected $user;

    /**
     * 我的报名记录
     */
    public function index()
    {
        $query = RoomSignupModel::with(
            [
                'room' => function ($query) {
                    $query->with(
                        [
                            // 'cates' => function ($query) {
                            //     $query->withField('id, name');
                            // },
                            'paper' => function ($query) {
                                $query->withField('id, title');
                            },
                        ]
                    );//->field('id,name,contents,cate_id,paper_id,');
                }
            ]
        )->where('user_id', $this->auth->id);

        // 状态查询
        $status = input('status', '');
        if (is_numeric($status)) {
            $query->where('status', $status);
        }

        $list = $query->order('id desc')->paginate();
        $this->success('', ['list' => $list]);
    }
}
