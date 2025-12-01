<?php

namespace app\admin\controller\exam;

use addons\exam\library\CertService;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 证书模板
 *
 * @icon fa fa-circle-o
 */
class CertTemplate extends Backend
{

    /**
     * CertTemplateModel模型对象
     * @var \app\admin\model\exam\CertTemplateModel
     */
    protected $model = null;
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\CertTemplateModel;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign('preview_url', url('preview'));
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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                ->with(['config'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {

                $row->getRelation('config')->visible(['name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 预览证书图片
     */
    public function preview()
    {
        $image     = input('image/s');
        $user_name = input('user_name');
        $score     = input('score');
        // dd($user_name, $score);

        if (!$image) {
            exit('请上传证书模板');
        }
        if (!$user_name) {
            exit('请设置证书姓名配置');
        }
        if (!$score) {
            exit('请设置证书分数配置');
        }

        $user_name = json_decode($user_name, true);
        $score     = json_decode($score, true);

        if (!file_exists(ROOT_PATH . 'public' . $image)) {
            exit('证书模板文件不存在，请重新上传');
        }

        $user_name['text'] = '姓名';
        $score['text']     = '100';

        $data = [
            'user_name' => $user_name,
            'score'     => $score,
            'image'     => $image,
            'name'      => 'preview',
        ];

        $image_url = CertService::generate($data);
        $image_url .= '?' . time();

        echo '<img src="' . $image_url . '" style="width: 100%;" />';
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

                $params = $this->valid($params);
                $result = false;
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
                $params = $this->valid($params);
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
     * @return mixed
     */
    protected function valid($params)
    {
        // dd($params);
        if (empty($params['min_score'])) {
            $this->error('请设置最低分数，最低不能小于等于0');
        }
        if (empty($params['field_config'])) {
            $this->error('请设置证书姓名及分数等配置');
        }
        if (empty(json_decode($params['field_config'], true))) {
            $this->error('证书配置数据有误');
        }

        // dd($params['field_config']);
        // $params['field_config'] = json_decode($params['field_config'], true);
        return $params;
    }
}
