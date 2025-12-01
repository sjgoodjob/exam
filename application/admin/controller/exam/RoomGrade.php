<?php

namespace app\admin\controller\exam;

use addons\exam\model\QuestionModel;
use addons\exam\model\RoomGradeModel;
use app\admin\model\exam\UserInfoModel;
use app\common\controller\Backend;
use think\Env;

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
                ->with(['user', 'cate', 'room', 'paper', 'signup1', 'school'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {

                $row->getRelation('user')->visible(['nickname']);
                $row->getRelation('cate')->visible(['name']);
                $row->getRelation('room')->visible(['name']);
                $row->getRelation('school')->visible(['name']);
                $row->getRelation('paper')->visible(['title']);
            }

            // $result = array("total" => $list->total(), "rows" => $list->items());

            $total = $list->total();
            $rows  = $list->items();

            if (Env::get('app.preview', false)) {
                foreach ($rows as &$row) {
                    if (!empty($row['signup1']['phone'])) {
                        $row['signup1']['phone'] = UserInfoModel::hideUserMobile($row['signup1']['phone']);
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
        $row = $this->model->get($ids, ['user', 'paper', 'cate', 'signup1', 'school']);
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

        if (Env::get('app.preview', false)) {
            if (!empty($row['user']['mobile'])) {
                $row['user']['mobile'] = UserInfoModel::hideUserMobile($row['user']['mobile']);
            }
        }

        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 刷新排行榜
     */
    public function rank()
    {
        $room_id = $this->request->request('room_id');

        // $user_ids = RoomGradeModel::where('room_id', $room_id)->group('user_id')->column('user_id');
        // $grades   = RoomGradeModel::with([
        //     // 'user'   => function ($query) {
        //     //     $query->field('id, nickname');
        //     // },
        //     // 'school' => function ($query) {
        //     //     $query->field('id, name');
        //     // },
        //     // 'signup' => function ($query) {
        //     //     $query->field('user_id, school_id, class_name, real_name');
        //     // },
        // ])->where('room_id', $room_id)
        //     ->where('user_id', 'in', $user_ids)
        //     ->field('id, user_id, max(score) as score, min(grade_time) as grade_time, school_id')
        //     ->group('user_id')
        //     ->order('score desc, grade_time asc')
        //     ->select();
        // // $grades   = collection($grades)->toArray();
        //
        // if (!$grades) {
        //     $this->error('暂无考试成绩');
        // }
        //
        // foreach ($grades as $key => $grade) {
        //     $rank = $key + 1;
        //     RoomGradeModel::where('id', $grade['id'])->update(['rank' => $rank]);
        // }

        RoomGradeModel::rankData($room_id);

        $this->success('刷新排行榜成功');
    }

    public function getByRank()
    {
        $room_id   = input('custom.room_id', 0);
        $real_name = input('real_name', '');

        $with = [
            'user'   => function ($query) {
                $query->field('id, nickname');
            },
            'school' => function ($query) {
                $query->field('id, name');
            },
            'signup' => function ($query) {
                $query->field('user_id, school_id, class_name, real_name');
            },
        ];

        if ($real_name) {
            $query = RoomGradeModel::hasWhere('signup', function ($query) use ($real_name) {
                $query->where('real_name', 'like', "%{$real_name}%");
            })->alias('RoomGradeModel')->with($with);
        } else {
            $query = RoomGradeModel::with($with)->alias('RoomGradeModel');
        }

        $grades = $query->where('RoomGradeModel.room_id', $room_id)
            ->where('rank', '>', 0)
            ->field('RoomGradeModel.id, RoomGradeModel.user_id, max(RoomGradeModel.score) as score, min(RoomGradeModel.grade_time) as grade_time, RoomGradeModel.school_id')
            ->group('RoomGradeModel.user_id')
            ->order('rank asc')
            ->select();

        $data = [];
        foreach ($grades as $grade) {
            $data[] = [
                'id'          => $grade['id'],
                'user_id'     => $grade['user_id'],
                'nickname'    => $grade['user']['nickname'] ?? '',
                'school_id'   => $grade['school_id'],
                'school_name' => $grade['school']['name'] ?? '',
                'class_name'  => $grade['signup']['class_name'] ?? '',
                'real_name'   => $grade['signup']['real_name'] ?? '',
                'score'       => $grade['score'],
                'grade_time'  => $grade['grade_time'],
            ];
        }

        return json([
            'list'  => $data,
            'total' => count($data),
        ]);
    }
}
