<?php

namespace addons\exam\controller;

use addons\exam\model\BaseModel;
use addons\exam\model\RoomGradeModel;


/**
 * 考场考试成绩接口
 */
class RoomGrade extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];

    /**
     * 获取成绩列表
     */
    public function index()
    {
        $list = RoomGradeModel::with(
            [
                // 'user'  => BaseModel::withSimpleUser(),
                'cate'  => BaseModel::withSimpleCate(),
                'paper' => BaseModel::withSimplePaper(),
                'room'  => BaseModel::withSimpleRoom(),
            ]
        )
            ->where('user_id', $this->auth->id)
            ->order('id desc')
            ->paginate(15, true);

        $this->success('', compact('list'));
    }

    /**
     * 排行榜
     */
    public function rank()
    {
        if (!$room_id = input('room_id/d', '0')) {
            $this->error('缺少考场信息');
        }

        $result = RoomGradeModel::rankData($room_id);
        $this->success('', $result);
    }
}
