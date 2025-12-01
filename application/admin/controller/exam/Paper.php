<?php

namespace app\admin\controller\exam;

use addons\exam\enum\PaperMode;
use app\admin\model\exam\PaperQuestionModel;
use app\admin\model\exam\QuestionModel;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\exception\DbException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;

/**
 * 试卷
 *
 * @icon fa fa-circle-o
 */
class Paper extends Backend
{

    /**
     * PaperModel模型对象
     *
     * @var \app\admin\model\exam\PaperModel
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\PaperModel;
        $this->view->assign("modeList", $this->model->getModeList());
        $this->view->assign("kindList", $this->model->getKindList());
        $this->view->assign("statusList", $this->model->getStatusList());
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
                ->with(['cate'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {

                $row->getRelation('cate')->visible(['name']);
            }

            $result = ["total" => $list->total(), "rows" => $list->items()];

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $this->valid($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    // dd($params);
                    $result = $this->model->allowField(true)->save($params);

                    // 保存试卷固定题目
                    $this->saveFixQuestion($this->model, $params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $this->valid($params);
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);

                    // 保存试卷固定题目
                    $this->saveFixQuestion($row, $params, 'edit');
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (\Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        if ($row['mode'] == PaperMode::FIX) {
            $row['questions'] = QuestionModel::getFixListByPaper($row['id'], ['cates']);
        }
        $this->view->assign("row", $row);
        $this->view->assign("configs", json_decode($row['configs'], true));
        return $this->view->fetch();
    }

    /**
     * 删除
     *
     * @param $ids
     * @return void
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function del($ids = null)
    {
        if (false === $this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        $ids = $ids ?: $this->request->post("ids");
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }
        $pk       = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = $this->model->where($pk, 'in', $ids)->select();

        foreach ($list as $item) {
            if ($this->model::getGradeCount($item['id'])) {
                $this->error("试卷【{$item['title']}】已有考试记录，无法删除");
            }
            if ($this->model::getRoomGradeCount($item['id'])) {
                $this->error("试卷【{$item['title']}】已有考场考试记录，无法删除");
            }
        }

        $count = 0;
        Db::startTrans();
        try {
            foreach ($list as $item) {
                $count += $item->delete();
            }
            Db::commit();
        } catch (PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were deleted'));
    }

    /**
     * 验证参数
     *
     * @param $params
     * @return void
     */
    protected function valid(&$params, $row = null)
    {
        if ($params['pass_score'] > $params['total_score']) {
            $this->error('及格分数不能大于总分');
        }

        $params['start_time'] = $params['start_time'] ?: 0;
        $params['end_time']   = $params['end_time'] ?: 0;

        if ($params['start_time']) {
            if ($params['start_time'] > $params['end_time']) {
                $this->error('开始时间不能大于结束时间');
            }
            // if (strtotime($params['start_time']) < time()) {
            //     $this->error('开始时间不能小于当前时间');
            // }
        }
        if ($params['end_time']) {
            if (!$params['start_time']) {
                $this->error('请先选择开始时间');
            }
        }

        // 固定选题模式
        if ($params['mode'] == 'FIX') {
            $params['questions'] = json_decode($params['questions'], true);
            if (!$params['questions']) {
                $this->error('请先选择题目');
            }
            if (count($params['questions']) < $params['quantity']) {
                $this->error('题目数量不能大于题目总数');
            }
        }

        $limit_time = 0;
        if ($params['limit_time_hour']) {
            $limit_time += $params['limit_time_hour'] * 60 * 60;
        }
        if ($params['limit_time_minute']) {
            $limit_time += $params['limit_time_minute'] * 60;
        }
        $params['limit_time'] = $limit_time;

        // 编辑时
        if ($row) {
            $grade_count      = $this->model::getGradeCount($row['id']);
            $room_grade_count = $this->model::getRoomGradeCount($row['id']);

            // 如果已有考试记录，不允许修改试卷主要数据
            if ($grade_count > 0 || $room_grade_count > 0) {
                if ($params['title'] != $row['title']) {
                    $this->error('试卷已有考试记录（含考场考试记录），不允许修改试卷名称');
                }
                if ($params['mode'] != $row['mode']) {
                    $this->error('试卷已有考试记录（含考场考试记录），不允许修改试卷选题模式');
                }
                if ($params['quantity'] != $row['quantity']) {
                    $this->error('试卷已有考试记录（含考场考试记录），不允许修改试卷题目数');
                }
                if ($params['total_score'] != $row['total_score']) {
                    $this->error('试卷已有考试记录（含考场考试记录），不允许修改试卷总分');
                }
                if ($params['pass_score'] != $row['pass_score']) {
                    $this->error('试卷已有考试记录（含考场考试记录），不允许修改及格分数');
                }
                if ($params['limit_time'] != $row['limit_time']) {
                    $this->error('试卷已有考试记录（含考场考试记录），不允许修改考试限时');
                }
            }
        }
    }

    /**
     * 保存固定选题
     *
     * @param $paper
     * @param $params
     * @return void
     */
    public function saveFixQuestion($paper, $params, $method = 'add')
    {
        if ($paper['mode'] != 'FIX') {
            return;
        }

        if ($method == 'edit') {
            PaperQuestionModel::where('paper_id', $paper['id'])->delete();
        }

        $questions = $params['questions'];
        $data      = [];
        foreach ($questions as $key => $question) {
            $data[] = [
                'paper_id'      => $paper['id'],
                'question_id'   => $question['id'],
                'score'         => $question['score'],
                'answer_config' => is_array($question['answer']) ? json_encode($question['answer'], JSON_UNESCAPED_UNICODE) : $question['answer'],
                'sort'          => $question['sort'] ?? ($key + 1),
                'createtime'    => time(),
            ];
        }

        (new PaperQuestionModel())->saveAll($data);
    }
}
