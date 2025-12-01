<?php

namespace addons\exam\controller;

use addons\exam\enum\CommonStatus;
use addons\exam\model\NoticeModel;


/**
 * 公告接口
 */
class Notice extends Base
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $user;

    /**
     * 列表
     */
    public function index()
    {
        $list = NoticeModel::where('status', CommonStatus::NORMAL)
            ->field('id, name, createtime')
            ->order('weigh desc')
            ->paginate(15, true);
        $this->success('', ['list' => $list]);
    }

    /**
     * 详情
     */
    public function detail()
    {
        if (!$id = input('id/d', '0')) {
            $this->error('缺少公告ID');
        }
        if (!$notice = NoticeModel::where('id', $id)->where('status', CommonStatus::NORMAL)->find()) {
            $this->error('公告信息不存在');
        }

        $this->success('', $notice);
    }
}
