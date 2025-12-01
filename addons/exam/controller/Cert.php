<?php

namespace addons\exam\controller;

use addons\exam\enum\GeneralStatus;
use addons\exam\model\CertModel;


/**
 * 证书接口
 */
class Cert extends Base
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];
    protected $user;

    /**
     * 列表
     */
    public function index()
    {
        $list = CertModel::with(['paper', 'room'])
            ->where('user_id', $this->auth->id)
            ->where('status', GeneralStatus::NORMAL)
            ->order('id desc')
            ->paginate(15, true);
        $this->success('', ['list' => $list]);
    }

    /**
     * 详情
     */
    public function detail()
    {
        if (!$id = input('id/d', '0')) {
            $this->error('缺少证书记录ID');
        }

        $cert = CertModel::with(['paper', 'room'])
            ->where('id', $id)
            ->where('user_id', $this->auth->id)
            ->where('status', GeneralStatus::NORMAL)
            ->find();
        if (!$cert) {
            $this->error('证书记录信息不存在');
        }

        $this->success('', $cert);
    }
}
