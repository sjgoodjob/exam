<?php

namespace app\admin\controller\exam;

use addons\exam\enum\PaperMode;
use addons\exam\model\QuestionModel;
use app\admin\model\exam\UserInfoModel;
use app\common\controller\Backend;
use think\Env;

/**
 * 考试成绩
 *
 * @icon fa fa-circle-o
 */
class Grade extends Backend
{

    protected $noNeedRight = ['*'];

    /**
     * GradeModel模型对象
     *
     * @var \app\admin\model\exam\GradeModel
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\GradeModel;

    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();

            $list = $this->model
                ->with(['cate', 'paper', 'user'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);
            
            foreach ($list as $row) {

                $row->getRelation('cate')->visible(['name']);
                $row->getRelation('paper')->visible(['title']);
            }

            // $result = array("total" => $list->total(), "rows" => $list->items());

            $total = $list->total();
            $rows  = $list->items();

            if (Env::get('app.preview', true)) {
                foreach ($rows as &$row) {
                    if (!empty($row['user']['mobile'])) {
                        $row['user']['mobile'] = UserInfoModel::hideUserMobile($row['user']['mobile']);
                    }
                }
            }

            $result = ["total" => $total, "rows" => $rows];

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 详情
     */
    public function detail($ids = null)
    {
        $row = $this->getDetail($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 详情
     */
    public function one($ids = null)
    {
        $row = $this->getDetail($ids);
        return json($row, 1);
    }

    /**
     * 获取并处理详情数据
     *
     * @param $ids
     * @return \app\admin\model\exam\GradeModel|array
     */
    protected function getDetail($ids)
    {
        $row = $this->model->get($ids, ['user', 'paper', 'cate']);

        if (
            (!isset($row['question_ids']) || !$row['question_ids']) ||
            (!isset($row['user_answers']) || !$row['user_answers']) ||
            (!isset($row['configs']) || !$row['configs'])
        ) {
            $this->error('答卷数据缺少，无法查看（可能是旧版本未记录数据导致）');
        }

        if ($row['mode'] == PaperMode::FIX) {
            $row['questions'] = QuestionModel::getFixListByPaper($row['paper_id'], ['materialQuestions.question']);
            // 合并材料题子题目
            $row['questions'] = QuestionModel::mergeMaterialQuestions($row['questions']);
        } else {
            $row['questions'] = QuestionModel::whereIn('id', $row['question_ids'])
                ->orderRaw("find_in_set(id, '" . $row['question_ids'] . "')")
                ->select();
        }
        $row['user_answers']    = json_decode($row['user_answers'], true);
        $row['configs']         = json_decode($row['configs'], true);
        $row['createtime_text'] = date('Y-m-d H:i:s', $row['createtime']);

        // 及格线
        $row['pass_score'] = $row['pass_score'] ?: $row['paper']['pass_score'];

        if (Env::get('app.preview', false)) {
            if (!empty($row['user']['mobile'])) {
                $row['user']['mobile'] = UserInfoModel::hideUserMobile($row['user']['mobile']);
            }
        }

        return $row;
    }
}
