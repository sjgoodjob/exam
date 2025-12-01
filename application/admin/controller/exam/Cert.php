<?php

namespace app\admin\controller\exam;

use addons\exam\enum\CertSource;
use addons\exam\enum\RoomSignupStatus;
use addons\exam\library\CertService;
use app\admin\model\exam\CertTemplateModel;
use app\admin\model\exam\RoomGradeModel;
use app\admin\model\exam\RoomSignupModel;
use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 证书管理
 *
 * @icon fa fa-circle-o
 */
class Cert extends Backend
{

    /**
     * CertModel模型对象
     * @var \app\admin\model\exam\CertModel
     */
    protected $model = null;

    protected $noNeedRight = ['add2'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\CertModel;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("sourceList", $this->model->getSourceList());
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
                ->with(['config', 'template', 'user', 'paper'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {

                $row->getRelation('config')->visible(['name']);
                $row->getRelation('template')->visible(['name']);
                $row->getRelation('user')->visible(['nickname']);
                $row->getRelation('paper')->visible(['title']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

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

                $params = $this->valid($params);

                $params['source']   = CertSource::MANUAL;
                $params['paper_id'] = 0;

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
     * 考场批量添加
     */
    public function add2()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }

                $room_grade_ids = $params['room_grade_ids'];
                if (!$room_grade_ids) {
                    $this->error('请选择考场报名人');
                }
                $room_grade_ids = explode(',', $room_grade_ids);
                if (empty($room_grade_ids)) {
                    $this->error('请选择考场报名人');
                }

                $cert_template = CertTemplateModel::get($params['cert_template_id']);
                if (empty($cert_template)) {
                    $this->error('证书模板不存在，请重新选择');
                }
                if (empty($cert_template['image']) || !file_exists(ROOT_PATH . 'public/' . $cert_template['image'])) {
                    $this->error('证书模板图片不存在，请重新选择');
                }
                $field_config = json_decode($cert_template['field_config'], true);
                if (empty($field_config)) {
                    $this->error('证书模板配置有误，请重新选择');
                }

                $user_name = $field_config['user_name'];
                $score     = $field_config['score'];

                $inserts = [];
                foreach ($room_grade_ids as $room_grade_id) {
                    // 成绩信息
                    $grade = RoomGradeModel::where('id', $room_grade_id)->order('score desc')->find();
                    if (empty($grade)) {
                        continue;
                    }

                    // 报名信息
                    $room_signup = RoomSignupModel::where('room_id', $params['room_id'])->where('user_id', $grade['user_id'])->where('status', RoomSignupStatus::ACCEPT)->find();
                    if (empty($room_signup)) {
                        continue;
                    }

                    $user_name['text'] = $room_signup['real_name'];
                    $score['text']     = $grade['score'];

                    $data      = [
                        'user_name' => $user_name,
                        'score'     => $score,
                        'image'     => $cert_template['image'],
                        'name'      => $user_name['text'] . '_' . $score['text'] . '_' . date('YmdHi'),
                    ];
                    $image_url = CertService::generate($data);

                    $inserts[] = [
                        'cert_config_id'   => $params['cert_config_id'],
                        'cert_template_id' => $params['cert_template_id'],
                        'user_id'          => $room_signup['user_id'],
                        'paper_id'         => 0,
                        'room_id'          => $params['room_id'],
                        'name'             => $cert_template['name'],
                        'user_name'        => $room_signup['real_name'],
                        'score'            => $grade['score'],
                        'image'            => $image_url,
                        'source'           => CertSource::MANUAL,
                        'createtime'       => time(),
                        'updatetime'       => time(),
                    ];
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

                    $result = $this->model->insertAll($inserts);
                    // $result = $this->model->allowField(true)->save($params);
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

    protected function valid($params)
    {
        if (empty($params['user_id'])) {
            $this->error('所属用户不能为空');
        }
        // if (empty($params['paper_id'])) {
        //     $this->error('所属试卷不能为空');
        // }
        if (empty($params['user_name'])) {
            $this->error('姓名不能为空');
        }
        if (empty($params['score'])) {
            $this->error('考试分数不能为空');
        }
        if (empty($params['cert_config_id'])) {
            $this->error('证书配置不能为空');
        }
        if (empty($params['cert_template_id'])) {
            $this->error('证书模板不能为空');
        }

        $cert_template = CertTemplateModel::get($params['cert_template_id']);
        if (empty($cert_template)) {
            $this->error('证书模板不存在，请重新选择');
        }
        if (empty($cert_template['image']) || !file_exists(ROOT_PATH . 'public/' . $cert_template['image'])) {
            $this->error('证书模板图片不存在，请重新选择');
        }

        $field_config = json_decode($cert_template['field_config'], true);
        if (empty($field_config)) {
            $this->error('证书模板配置有误，请重新选择');
        }

        $user_name = $field_config['user_name'];
        $score     = $field_config['score'];

        $user_name['text'] = $params['user_name'];
        $score['text']     = $params['score'];

        $data = [
            'user_name' => $user_name,
            'score'     => $score,
            'image'     => $cert_template['image'],
            'name'      => $user_name['text'] . '_' . $score['text'] . '_' . date('YmdHi'),
        ];

        $image_url       = CertService::generate($data);
        $params['image'] = $image_url;
        $params['name']  = $cert_template['name'];

        return $params;
    }
}
