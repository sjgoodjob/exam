<?php

namespace addons\exam\controller;

use addons\exam\enum\CateUses;
use addons\exam\enum\CommonStatus;
use addons\exam\enum\GeneralStatus;
use addons\exam\model\QuestionCollectModel;
use addons\exam\model\QuestionModel;
use addons\exam\model\UserModel;
use app\admin\model\exam\CateModel;
use app\admin\model\exam\ConfigInfoModel;
use app\admin\model\exam\QuestionWrongModel;


/**
 * 试题接口
 */
class Question extends Base
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    /**
     * 看题模式
     */
    public function lookPage()
    {
        $model = new QuestionModel();
        $total = $model->where('cate_id', $this->request->param('cate_id'))->count('id');
        $this->success('', compact('total'));
    }

    /**
     * 练习模式
     */
    public function train()
    {
        $param            = $this->request->param();
        $param['user_id'] = $this->auth->id;

        // 验证是否需要绑定手机号
        UserModel::isMustBindMobile($this->auth->getUser());

        if (!input('just_get_count/d', 0)) {
            if ($cate_id = input('cate_id/d')) {
                CateModel::checkUserHasCatePermission($cate_id, $this->auth->id);
            }
        }

        $list = QuestionModel::getList($param);
        $this->success('', $list);
    }

    /**
     * 根据关键词模糊查询10条题目
     */
    public function search()
    {
        $query = QuestionModel::with(
            [
                'cates'          => function ($query) {
                    $query->field('id,name');
                },
                'materialParent' => function ($query) {
                    $query->field('id,title');
                },
            ]
        )->where('status', CommonStatus::NORMAL);

        // 非会员，只显示公共题库
        if (!UserModel::isMember($this->auth->id)) {
            $cate_ids = CateModel::where('status', GeneralStatus::NORMAL)->where('uses', CateUses::ALL)->column('id');
            if ($cate_ids) {
                $query->where('cate_id', 'in', $cate_ids);
            }
        }

        if ($keyword = input('keyword/s', '', 'trim,strip_tags,htmlspecialchars,xss_clean')) {
            if (mb_strlen($keyword) < 2) {
                $this->error('请输入不少于2个字的关键词进行搜索');
            }

            $query->where('title', 'like', '%' . $keyword . '%');
        }

        if ($sort_type = input('sort_type/s')) {
            $query->order($sort_type);
        }

        if (input('sort_rand/d', 0)) {
            $query->orderRaw('rand()');
        }

        $list = $query->paginate(15, true)->toArray();
        // 最多搜索5页
        if (input('page/d') >= 5) {
            $list['has_more'] = false;
        }

        $this->success('', ['list' => $list]);
    }

    /**
     * 试题详情
     */
    public function detail($id)
    {
        $this->success('', (new QuestionModel)->get($id));
    }

    /**
     * 收藏列表
     */
    public function collectList()
    {
        $user_id         = $this->auth->id;
        $collectQuestion = new QuestionCollectModel();

        $list  = $collectQuestion::with([
            'question' => function ($query) {
                $query->with('materialParent');
            },
        ])->where('user_id', $user_id)->order('id desc')->paginate(999, true)->toArray();
        $total = $collectQuestion::where('user_id', $user_id)->count();

        $question_count = 0;
        foreach ($list['data'] as &$item) {
            if (!empty($item['question'])) {
                if (!empty($item['question']['material_parent'])) {
                    // 设置材料题题干
                    $item['question']['material_title'] = $item['question']['material_parent']['title'];
                }

                $question_count++;
            }
        }

        if (!$question_count) {
            $list['data'] = [];
            $total        = 0;
        }

        // $list['data'] = QuestionModel::setQuestionsMaterialParent($list['data']);

        $this->success('', compact('list', 'total'));
    }

    /**
     * 添加收藏
     */
    public function collectAdd()
    {
        if (!$question_id = input('question_id/d', 0)) {
            $this->error('缺少题目ID');
        }
        if (!QuestionModel::where('id', $question_id)->count()) {
            $this->error('题目数据不存在');
        }

        $res = QuestionCollectModel::updateOrCreate(
            [
                'user_id'     => $this->auth->id,
                'question_id' => $question_id,
            ],
            [
                'user_id'     => $this->auth->id,
                'question_id' => $question_id,
            ]
        );
        $this->success('收藏成功', $res);
    }

    /**
     * 取消收藏
     */
    public function collectCancel()
    {
        if (!$question_id = input('question_id/d', 0)) {
            $this->error('缺少题目ID');
        }

        QuestionCollectModel::where('question_id', $question_id)->where('user_id', $this->auth->id)->delete();
        $this->success('取消收藏成功');
    }

    /**
     * 获取错题列表
     */
    public function wrongList()
    {
        $user_id = $this->auth->id;

        // 判断是否开启会员限制
        $member_show_wrong = ConfigInfoModel::getConfigInfo('member_config', 'member_show_wrong', 0);
        if ($member_show_wrong) {
            if (!UserModel::isMember($user_id)) {
                $this->error('该功能仅针对会员开放，请开通会员后再试', ['need_open' => true]);
            }
        }

        if ($ids = input('question_ids')) {
            $ids = explode(',', $ids);
            // 必须是int类型
            $ids = array_filter(array_map('intval', $ids));
            if (!$ids) {
                $this->error('题目ID有误');
            }

            $count = count($ids);
            $total = QuestionWrongModel::where('user_id', $user_id)->whereIn('question_id', $ids)->count();
            $list  = $total ? QuestionWrongModel::with([
                'question' => function ($query) {
                    $query->with('materialParent');
                },
            ])
                ->whereIn('question_id', $ids)
                ->where('user_id', $user_id)
                ->orderRaw("createtime desc, find_in_set(question_id, '" . implode(',', $ids) . "')")// 保持原有顺序
                ->limit($count)
                ->select() : [];

            $list = $list ? collection($list)->toArray() : [];
            $list = ['total' => $count, 'data' => $list];
        } else {
            $total = QuestionWrongModel::where('user_id', $user_id)->count();
            $list  = $total ? QuestionWrongModel::with([
                'question' => function ($query) {
                    // $query->with('materialParent');
                },
            ])
                ->where('user_id', $user_id)
                ->order('id desc')
                ->paginate(999, true)->toArray() : [];
        }

        if (!empty($list['data'])) {
            $questions      = [];
            $question_count = 0;
            foreach ($list['data'] as $item) {
                if (!empty($item['question'])) {
                    $questions[] = array_merge($item['question'], [
                        'wrong_id'    => $item['id'],
                        'user_answer' => $item['user_answer'],
                        'source'      => $item['kind'],
                    ]);

                    $question_count++;
                }
            }

            if ($question_count) {
                // 设置材料题题干
                $questions = QuestionModel::setQuestionsMaterialParent($questions);
                // 设置收藏状态
                $list['data'] = \addons\exam\model\QuestionModel::isCollected($user_id, $questions);
            } else {
                $list['data'] = [];
                $total        = 0;
            }
        } else {
            $list['data'] = [];
            $total        = 0;
        }

        $this->success('', compact('list', 'total'));
    }

    /**
     * 记录错题
     */
    public function wrongAdd()
    {
        if (!$question_id = input('question_id/d', 0)) {
            $this->error('缺少题目ID');
        }

        $question = QuestionModel::get($question_id);
        if (!$question) {
            $this->error('题目数据不存在');
        }

        $source = input('source/s', 'TRAINING');
        if (in_array($question['kind'], ['FILL', 'SHORT'])) {
            $user_answer = input('user_answer/a', []);
        } else {
            $user_answer = input('user_answer/s', '');
        }

        QuestionModel::recordWrong($question['kind'], $question_id, $this->auth->id, $user_answer, $source, [
            'cate_id' => $question['cate_id'],
        ]);
        $this->success('记录成功');
    }

    /**
     * 删除错题
     */
    public function wrongDelete()
    {
        if (!$question_id = input('question_id/d', 0)) {
            $this->success('缺少错题ID');
        }

        QuestionWrongModel::where('question_id', $question_id)->where('user_id', $this->auth->id)->delete();
        $this->success('删除成功');
    }

    /**
     * 清空所有错题
     */
    public function wrongClear()
    {
        QuestionWrongModel::where('user_id', $this->auth->id)->delete();
        $this->success('删除成功');
    }

}
