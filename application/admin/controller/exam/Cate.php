<?php

namespace app\admin\controller\exam;

use app\admin\model\exam\CateModel;
use app\admin\model\exam\SubjectModel;
use app\common\controller\Backend;
use fast\Tree;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 试题分类
 * @icon fa fa-circle-o
 */
class Cate extends Backend
{
    protected $noNeedRight = ['selectpage', 'getQuestionCate'];

    /**
     * CateModel模型对象
     * @var \app\admin\model\exam\CateModel
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\CateModel;
        $query       = \app\admin\model\exam\CateModel::with([
            'subject' => function ($query) {
                $query->field('id,name');
            }
        ])->order('sort desc,id desc');

        $kind = input('kind/s', 'all');
        if ($kind != 'all') {
            $query->where('kind', $kind);
        }

        $tree = Tree::instance();
        $tree->init(collection($query->select())->toArray(), 'parent_id');
        $this->parentlist = $tree->getTreeList($tree->getTreeArray(0), 'name');

        $this->view->assign("kindList", $this->model->getKindList());
        $this->view->assign("parentList", $this->parentlist);
        $this->view->assign("usesList", $this->model->getUsesList());
        $this->view->assign("isFreeList", $this->model->getIsFreeList());
        $this->view->assign("isLookList", $this->model->getIsLookList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function import()
    {
        parent::import();
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
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            // if ($this->request->request('keyField')) {
            //     return $this->selectpage();
            // }

            /*list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $addWhere = [];
            if ($this->request->request('searchKey')) {
                $addWhere[] = [
                    $this->request->request('searchKey'),
                    [
                        'in',
                        $this->request->request('searchKey')
                    ]
                ];
            }

            $total = $this->model
                ->where($where)
                ->where($addWhere)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where($addWhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list   = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);*/

            $search = $this->request->request("search");
            $kind   = $this->request->request("kind");

            //构造父类select列表选项数据
            $list = [];

            foreach ($this->parentlist as $k => $v) {
                if ($search) {
                    if ($v['kind'] == $kind) {
                        $list[] = $v;
                    }
                } else {
                    $list[] = $v;
                }
            }

            $total  = count($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * Selectpage搜索
     * @internal
     */
    public function selectpage()
    {
        return parent::selectpage();
    }

    /**
     * 获取有题目的分类
     * @internal
     */
    public function getQuestionCate()
    {
        if ($cate_ids = $this->request->request("keyValue", "")) {
            $list = CateModel::whereIn('id', $cate_ids)->select();
        } else {
            $ids  = Db::name('exam_question')->whereNull('deletetime')->group('cate_id')->field('cate_id')->select();
            $list = $ids ? CateModel::whereIn('id', array_column($ids, 'cate_id'))->select() : [];
            // $list = $ids ? CateModel::whereIn('id', array_column($ids, 'cate_id'))->select() : [];
        }
        return json(['list' => $list, 'total' => count($list)]);
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

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                $this->validParams($params);
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }

                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
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
                $params = $this->preExcludeFields($params);
                $result = false;
                $this->validParams($params, $row);
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }

                    // 子级跟随父级的付费设置
                    $child_ids = CateModel::getChildId($ids);
                    if ($child_ids) {
                        $data = [
                            'uses'    => $params['uses'],
                            'is_free' => $params['is_free']
                        ];

                        // 付费设置
                        if ($params['is_free'] != 1) {
                            $data['price'] = $params['price'];
                            $data['days']  = $params['days'];
                        }

                        CateModel::whereIn('id', $child_ids)->update($data);
                    }

                    $result = $row->allowField(true)->save($params);
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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 验证参数
     * @param $params
     */
    protected function validParams(&$params, $row = null)
    {
        $params['price'] = $params['price'] ?? 0;
        if ($params['price'] < 0) {
            $this->error('开通价格不能小于0');
        }

        if (!empty($params['subject_id'])) {
            $subject = SubjectModel::get($params['subject_id']);
            if (!$subject) {
                $this->error('所属科目数据不存在');
            }
            if (!$subject['parent_id']) {
                $this->error('所属科目必须是二级科目');
            }
        }

        $params['parent_id'] = $params['parent_id'] ?? 0;

        // 设置层级
        if (!$params['parent_id']) {
            $params['level'] = 1;
        } else {
            $parent = CateModel::get($params['parent_id']);

            // 编辑时
            if ($row) {
                if ($params['parent_id'] == $row['id']) {
                    $this->error('不能将当前分类设置为父级分类');
                }
                if ($parent['kind'] != $params['kind']) {
                    $this->error('不能将当前分类设置为其他种类的下级');
                }
                $child_ids = CateModel::where('parent_id', $row['id'])->column('id');
                if (in_array($params['parent_id'], $child_ids)) {
                    $this->error('不能将当前分类的子级设置为当前分类的父级');
                }
            }

            if ($parent['level'] == 1) {
                $params['level'] = 2;
            } else if ($parent['level'] == 2) {
                $params['level'] = 3;
            } else {
                $this->error('错误：最多添加3级分类，请重新选择父级类别');
            }
        }
    }
}
