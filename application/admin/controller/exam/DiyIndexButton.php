<?php

namespace app\admin\controller\exam;

use addons\exam\library\FrontService;
use app\common\controller\Backend;
use think\Db;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 自定义首页按钮
 *
 * @icon fa fa-circle-o
 */
class DiyIndexButton extends Backend
{
    protected $noNeedRight = ['*'];

    /**
     * DiyIndexButtonModel模型对象
     * @var \app\admin\model\exam\DiyIndexButtonModel
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\DiyIndexButtonModel;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("pageStyleList", $this->model->getPageStyleList());
        $this->view->assign('pages', FrontService::getNoParamsPages());
    }


    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 添加
     *
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
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
                $this->model->validateFailException()->validate($validate);
            }
            $result = $this->model->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if ($result === false) {
            $this->error(__('No rows were inserted'));
        }
        $this->success();
    }

    /**
     * 编辑
     *
     * @param $ids
     * @return string
     * @throws DbException
     * @throws \think\Exception
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $this->view->assign('row', $row);
            return $this->view->fetch();
        }
        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $this->valid($params);

        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }
            $result = $row->allowField(true)->save($params);
            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        if (false === $result) {
            $this->error(__('No rows were updated'));
        }
        $this->success();
    }

    /**
     * 校验参数
     */
    protected function valid($params)
    {
        if (empty($params['name'])) {
            $this->error('名称不能为空');
        }
        if (empty($params['path'])) {
            $this->error('路径不能为空');
        }
        if (empty($params['type'])) {
            $this->error('类型不能为空');
        }
        if ($params['type'] == 'icon') {
            if (empty($params['icon'])) {
                $this->error('图标不能为空');
            }
            if (empty($params['color'])) {
                $this->error('图标颜色不能为空');
            }
        }
        if ($params['type'] == 'image' && empty($params['image'])) {
            $this->error('图片不能为空');
        }
        // if ($params['status']) {
        //     $count = $this->model
        //         ->where('status', 1)
        //         ->count();
        //     if ($count > 8) {
        //         $this->error('最多只能启用8个按钮');
        //     }
        // }
    }

    /**
     * 初始化数据
     */
    public function initdata()
    {
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }

        $page_style = $this->request->post('page_style');
        if (!$page_style) {
            $this->error('请选择页面风格');
        }

        $data = $this->model::getInitDiyData($page_style);
        if (!$data) {
            $this->error('没有可以初始化的数据');
        }

        foreach ($data as &$item) {
            $item['page_style'] = $page_style;
            $item['createtime'] = time();
        }

        $this->model->insertAll($data);
        $this->success('初始化成功');
    }
}
