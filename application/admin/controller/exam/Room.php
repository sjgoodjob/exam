<?php

namespace app\admin\controller\exam;

use addons\exam\enum\RoomMode;
use app\admin\model\exam\CertConfigModel;
use app\admin\model\exam\CertTemplateModel;
use app\admin\model\exam\ConfigInfoModel;
use app\admin\model\exam\RoomModel;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 考试考场
 *
 * @icon fa fa-circle-o
 */
class Room extends Backend
{

    /**
     * RoomModel模型对象
     *
     * @var \app\admin\model\exam\RoomModel
     */
    protected $model = null;

    protected $noNeedRight = ['createH5Qrcode'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\RoomModel;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("signupModeList", $this->model->getSignupModeList());
        $this->view->assign("isMakeupList", $this->model->getIsMakeupList());
        $this->view->assign("isCreateQrcodeH5List", $this->model->getIsCreateQrcodeH5List());
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
                ->with(['paper', 'cate', 'subject'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {

                $row->getRelation('paper')->visible(['title']);
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

                    // 验证提交数据
                    $this->validSubmit($params);
                    $result = $this->model->allowField(true)->save($params);
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
                    // 创建H5二维码
                    if (!empty($params['is_create_qrcode_h5'])) {
                        $qrcode_url = RoomModel::createH5Qrcode($this->model->id);
                        $this->model->save(['qrcode_h5' => $qrcode_url]);
                    }
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
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }

                    // 验证提交数据
                    $this->validSubmit($params);
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
                    // 创建H5二维码
                    // if ($params['is_create_qrcode_h5']) {
                    //     $qrcode_url = RoomModel::createH5Qrcode($row->id);
                    //     $row->save(['qrcode_h5' => $qrcode_url]);
                    // }

                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $h5_url = ConfigInfoModel::getH5Url();
        $this->view->assign("h5_url", $h5_url);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 验证提交数据
     *
     * @param $params
     * @return void
     */
    protected function validSubmit(&$params)
    {
        $start_time = $params['start_time'] ?? '';
        $end_time   = $params['end_time'] ?? '';
        if ($start_time > $end_time) {
            throw new \Exception('考场开始时间不能大于结束时间');
        }

        $signup_mode = $params['signup_mode'] ?? RoomMode::GENERAL;
        $password    = $params['password'] ?? '';
        if ($signup_mode == RoomMode::PASSWORD && !$password) {
            throw new \Exception('密码模式必须填写考场密码');
        }

        $is_makeup    = $params['is_makeup'] ?? 0;
        $makeup_count = $params['makeup_count'] ?? 0;
        if ($is_makeup) {
            if (!$makeup_count) {
                throw new \Exception('开启补考模式，请填写大于0的补考次数');
            }
        } else {
            // 不补考，补考次数默认0
            $params['makeup_count'] = 0;
        }

        if (!empty($params['cert_config_id'])) {
            $cert_config = CertConfigModel::get([
                'id'     => $params['cert_config_id'],
                'status' => '1',
            ]);
            if (!$cert_config) {
                throw new \Exception('不存在或已禁用，请重新选择');
            }
            if (CertTemplateModel::where('cert_config_id', $cert_config['id'])->where('status', '1')->count() <= 0) {
                throw new \Exception("证书配置【{$cert_config['name']}】未配置证书模板，请先添加证书模板");
            }
        }
    }

    /**
     * 生成二维码
     */
    public function createH5Qrcode()
    {
        if (!$room_id = input('room_id/d', 0)) {
            $this->error('参数错误');
        }

        $qrcode_url = RoomModel::createH5Qrcode($room_id);
        $this->success('生成二维码成功', '', $qrcode_url);
    }
}
