<?php

namespace addons\exam\controller;

use addons\exam\enum\CommonStatus;
use addons\exam\model\NewsModel;


/**
 * 学习动态接口
 */
class News extends Base
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $user;

    /**
     * 列表
     */
    public function index()
    {
        $list = NewsModel::where('status', CommonStatus::NORMAL)
            ->field('id, cover_image, images, name, createtime')
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
            $this->error('缺少学习动态ID');
        }
        if (!$news = NewsModel::where('id', $id)->where('status', CommonStatus::NORMAL)->find()) {
            $this->error('学习动态信息不存在');
        }

        $this->success('', $news);
    }
}
