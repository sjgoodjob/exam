<?php

namespace app\admin\controller\exam;

use addons\exam\library\FrontService;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 参数配置
 * @icon fa fa-circle-o
 */
class ConfigInfo extends Backend
{
    protected $noNeedRight = ['*'];
    /**
     * ConfigInfoModel模型对象
     * @var \app\admin\model\exam\ConfigInfoModel
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\ConfigInfoModel;

    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        $isUpdate    = false;
        $this->model = $this->model->get(1);
        if (!empty($this->model['id'])) {
            $isUpdate = true;
            $configs  = $this->model->toArray();
            unset($configs['id']);
            $row = [];
            foreach ($configs as $key => $val) {
                $val = $val ? json_decode($val, true) : [];
                $row = array_merge($row, $val);
            }
            //            dump($row);exit;
            $this->assign('row', $row);
            $this->assign('config_id', $this->model['id']);
        } else {
            $this->model = new \app\admin\model\exam\ConfigInfoModel;
            $this->model->save(['id' => 1]);
        }

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {

                    $type = $this->request->param('type');
                    $data = $params;


                    //转json存到字段
                    $params["{$type}_config"] = json_encode($data);

                    $result = $this->model->isUpdate($isUpdate)->allowField(true)->save($params);

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
     * 设置前端跳转页面
     */
    public function frontend()
    {
        if ($this->request->isPost()) {
            $params   = $this->request->post("info/a");
            $full_url = $params['url'] ?? '';
            if (isset($params['params'])) {
                $full_url = FrontService::buildUrl($full_url, $params['params']);
            }
            $params['full_url'] = $full_url;
            $this->success('', '', $params);
        } else {
            $pages = FrontService::PAGES;
            $this->assign('pages', $pages);
            return $this->view->fetch();
        }
    }
}
