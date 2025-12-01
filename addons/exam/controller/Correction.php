<?php

namespace addons\exam\controller;

use addons\exam\model\CorrectionQuestionModel;
use app\admin\model\exam\CorrectionTypeModel;


/**
 * 纠错接口
 */
class Correction extends Base
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = ['*'];
    protected $user;

    /**
     * 纠错类型
     */
    public function types()
    {
        $types = CorrectionTypeModel::all();
        $this->success('', ['types' => $types]);
    }

    /**
     * 提交纠错
     */
    public function submit()
    {
        $question_id = input('question_id/d');
        // $type_ids    = input('type_ids/a', []);
        $type_names = input('type_names/a', []);
        $remark     = input('remark/s', '', 'trim,strip_tags,htmlspecialchars,xss_clean');

        if (!$question_id) {
            $this->error(__('缺少题目ID参数'));
        }
        // if (!$type_ids) {
        //     $this->error(__('请选择纠错类型'));
        // }
        if (!$type_names) {
            $this->error(__('请选择纠错类型'));
        }

        CorrectionQuestionModel::create([
            'user_id'     => $this->auth->id,
            'question_id' => $question_id,
            'type_ids'    => '',//implode(',', $type_ids),
            'type_names'  => implode(',', $type_names),
            'remark'      => $remark,
        ]);

        $this->success('提交成功，感谢您的反馈');
    }

    /**
     * 纠错反馈列表
     */
    public function list()
    {
        $list = CorrectionQuestionModel::with([
            'question' => function ($query) {
                $query->with([
                    'cates' => function ($query) {
                        $query->field('id,name');
                    },
                ])->field('id,cate_id,kind,title');
            }
        ])->where('user_id', $this->auth->id)->order('id', 'desc')->paginate(15, true);
        $this->success('', $list);
    }
}
