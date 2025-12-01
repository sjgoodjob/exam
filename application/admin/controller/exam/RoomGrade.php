<?php

namespace app\admin\controller\exam;

use addons\exam\model\QuestionModel;
use app\common\controller\Backend;

/**
 * 考场考试成绩
 *
 * @icon fa fa-circle-o
 */
class RoomGrade extends Backend
{
    protected $noNeedRight = ['*'];

    /**
     * RoomGradeModel模型对象
     *
     * @var \app\admin\model\exam\RoomGradeModel
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\RoomGradeModel;
        $this->view->assign("isPassList", $this->model->getIsPassList());
        $this->view->assign("isMakeupList", $this->model->getIsMakeupList());
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
                ->with(['user', 'cate', 'room', 'paper', 'signup1'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {

                $row->getRelation('user')->visible(['nickname']);
                $row->getRelation('cate')->visible(['name']);
                $row->getRelation('room')->visible(['name']);
                $row->getRelation('paper')->visible(['title']);
            }

            $result = ["total" => $list->total(), "rows" => $list->items()];

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 详情
     */
    public function detail($ids = null)
    {
        $row = $this->model->get($ids, ['user', 'paper', 'cate']);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        if (
            (!isset($row['question_ids']) || !$row['question_ids']) ||
            (!isset($row['user_answers']) || !$row['user_answers']) ||
            (!isset($row['configs']) || !$row['configs'])
        ) {
            $this->error('答卷数据缺少，无法查看（可能是旧版本未记录数据导致）');
        }

        $row['questions']       = QuestionModel::whereIn('id', $row['question_ids'])
            ->orderRaw("find_in_set(id, '" . $row['question_ids'] . "')")
            ->select();
        $row['user_answers']    = json_decode($row['user_answers'], true);
        $row['configs']         = json_decode($row['configs'], true);
        $row['createtime_text'] = date('Y-m-d H:i:s', $row['createtime']);

        // 及格线
        $row['pass_score'] = $row['pass_score'] ?: $row['paper']['pass_score'];

        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
