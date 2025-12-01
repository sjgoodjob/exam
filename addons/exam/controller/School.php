<?php

namespace addons\exam\controller;


use addons\exam\enum\GeneralStatus;
use app\admin\model\exam\SchoolModel;

/**
 * 学校接口
 */
class School extends Base
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $user;

    /**
     * 学校列表
     */
    public function index()
    {
        $list = SchoolModel::where('status', GeneralStatus::NORMAL)->field('id, name')->order('weigh desc')->select();
        $this->success('', ['list' => $list]);
    }
}
