<?php

namespace app\admin\controller\exam;

use addons\exam\enum\UserScoreType;
use addons\exam\model\UserModel;
use app\admin\model\Admin;
use app\admin\model\exam\MemberConfigModel;
use app\admin\model\exam\SubjectModel;
use app\admin\model\exam\UserInfoModel;
use app\admin\model\exam\UserScoreLogModel;
use app\common\controller\Backend;
use think\Db;
use think\Env;
use think\exception\DbException;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 用户信息
 * @icon fa fa-circle-o
 */
class UserInfo extends Backend
{
    protected $noNeedRight = ['manualmember', 'manualscore', 'manualmemberset'];

    /**
     * UserInfoModel模型对象
     * @var \app\admin\model\exam\UserInfoModel
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\UserInfoModel;
        $this->view->assign("typeList", $this->model->getTypeList());
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
                ->with(['user', 'memberConfig'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->getRelation('user')->visible(['nickname', 'mobile']);
            }

            $total = $list->total();
            $rows  = $list->items();

            if (Env::get('app.preview', false)) {
                foreach ($rows as &$row) {
                    if (!empty($row['user']['mobile'])) {
                        $row['user']['mobile'] = UserInfoModel::hideUserMobile($row['user']['mobile']);
                    }
                }
            }

            $result = array("total" => $total, "rows" => $rows);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 手动设置会员过期时间
     * @param $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function manualmember($ids = null)
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

                // 验证会员过期时间
                $expire_time = $params['expire_time'];
                if (!$expire_time) {
                    $this->error('请设置会员过期时间');
                }
                $expire_time_int = strtotime($expire_time);
                if (!$expire_time_int || !is_numeric($expire_time_int)) {
                    $this->error('会员过期时间格式不正确');
                }

                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }

                    // 保存会员过期时间
                    $params['expire_time'] = $expire_time_int;
                    $result                = $row->allowField(true)->save($params);
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
     * 手动操作会员积分
     * @param $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function manualscore($ids = null)
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

                // 验证积分
                $score = $params['score'];
                if (!$score || !is_numeric($score)) {
                    $this->error('请填写正确的积分数');
                }

                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }

                    // 记录操作人
                    $admin = Admin::get(intval($this->auth->id));
                    $memo  = "管理员【{$admin['nickname']}】操作";
                    if ($score > 0) {
                        $after_score = UserScoreLogModel::increment($row['user_id'], $score, UserScoreType::MANUAL, $admin, $memo);
                    } else {
                        $after_score = UserScoreLogModel::decrement($row['user_id'], abs($score), UserScoreType::MANUAL, $admin, $memo);
                    }

                    $result = $after_score != $row['score'];
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

    // /**
    //  * 手动创建用户
    //  */
    // public function create()
    // {
    //
    // }

    /**
     * 添加
     */
    public function add()
    {
        // $this->model = new User();
        if (false === $this->request->isPost()) {
            return $this->view->fetch();
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $params = $this->preExcludeFields($params);
        if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
            $params[$this->dataLimitField] = $this->auth->id;
        }

        $this->checkOnlySubjectId($params['only_subject_ids']);
        Db::startTrans();
        try {
            //是否采用模型验证
            // if ($this->modelValidate) {
            //     $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
            //     $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
            //     $this->model->validateFailException()->validate($validate);
            // }
            // $result = $this->model->allowField(true)->save($params);

            // 注册
            $user = UserModel::fastRegister($params['username'], $params['nickname'], $params['avatar'], $params['gender'], $params['password'], $params['mobile']);

            // 会员过期时间
            $expire_time = 0;
            if ($params['member_config_id']) {
                $member_config = MemberConfigModel::get($params['member_config_id']);
                if ($member_config) {
                    $expire_time = time() + $member_config['days'] * 86400;
                }
            }

            UserInfoModel::create([
                'user_id'          => $user['id'],
                'score'            => $params['score'] ?? 0,
                'score_inc'        => $params['score'] ?? 0,
                'member_config_id' => $params['member_config_id'],
                'expire_time'      => $expire_time,
                'only_subject_ids' => $params['only_subject_ids'],
            ]);

            Db::commit();
        } catch (ValidateException|PDOException|Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        // if ($result === false) {
        //     $this->error(__('No rows were inserted'));
        // }
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
        $row = $this->model->get($ids, ['user']);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds) && !in_array($row[$this->dataLimitField], $adminIds)) {
            $this->error(__('You have no permission'));
        }
        if (false === $this->request->isPost()) {
            $row['username'] = $row['user']['username'];
            $row['nickname'] = $row['user']['nickname'];
            $row['avatar']   = $row['user']['avatar'];
            $row['gender']   = $row['user']['gender'];
            $row['mobile']   = $row['user']['mobile'];
            // dd($row->toArray());

            $this->view->assign('row', $row);
            return $this->view->fetch();
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);
        $this->checkOnlySubjectId($params['only_subject_ids']);
        $result = false;
        Db::startTrans();
        try {
            //是否采用模型验证
            if ($this->modelValidate) {
                $name     = str_replace("\\model\\", "\\validate\\", get_class($row['user']));
                $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                $row->validateFailException()->validate($validate);
            }

            if ($params['password']) {
                $row['user']->password = $params['password'];
            }

            $row['user']['username'] = $params['username'];
            $row['user']['nickname'] = $params['nickname'];
            $row['user']['avatar']   = $params['avatar'];
            $row['user']['gender']   = $params['gender'];
            $row['user']['mobile']   = $params['mobile'];
            $row['user']->save();

            if ($params['member_config_id']) {
                // 会员过期时间
                $params['expire_time'] = $row['expire_time'];
                if ($params['member_config_id'] != $row['member_config_id']) {
                    $member_config = MemberConfigModel::get($params['member_config_id']);
                    if ($member_config) {
                        $params['expire_time'] = time() + $member_config['days'] * 86400;
                    }
                }
            } else {
                $params['expire_time'] = 0;
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
     * 批量设置会员
     * @param $ids
     * @return string|void
     */
    public function manualmemberset($ids = null)
    {
        $ids = $ids ?: $this->request->post('ids');
        if (empty($ids)) {
            $this->error(__('Parameter %s can not be empty', 'ids'));
        }

        if (!$this->request->isPost()) {
            $this->view->assign("ids", $ids);
            return $this->view->fetch();
        }

        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }

        $params = $this->request->post('row/a');
        if (empty($params)) {
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $params = $this->preExcludeFields($params);

        // 验证会员过期时间
        $expire_time = $params['expire_time'];
        if (!$expire_time) {
            $this->error('请设置会员过期时间');
        }
        $expire_time_int = strtotime($expire_time);
        if (!$expire_time_int || !is_numeric($expire_time_int)) {
            $this->error('会员过期时间格式不正确');
        }

        $count = $this->model->whereIn('id', $ids)->update(['expire_time' => $expire_time_int]);

        if ($count) {
            $this->success();
        }
        $this->error(__('No rows were updated'));
    }

    /**
     * 验证选择的科目
     * @param $only_subject_ids
     * @return void
     */
    protected function checkOnlySubjectId($only_subject_ids)
    {
        if ($only_subject_ids) {
            $subject_ids = explode(',', $only_subject_ids);
            $subjects    = SubjectModel::where('id', 'in', $subject_ids)->where('status', 1)->select();
            if (count($subjects) != count($subject_ids)) {
                $this->error('可用科目设置不正确');
            }
            foreach ($subjects as $subject) {
                if ($subject['parent_id'] == 0) {
                    $this->error('只能选择二级科目');
                }
            }
        }
    }
}
