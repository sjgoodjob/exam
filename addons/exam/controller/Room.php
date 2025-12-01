<?php

namespace addons\exam\controller;

use addons\exam\enum\CommonStatus;
use addons\exam\enum\GeneralStatus;
use addons\exam\enum\RoomMode;
use addons\exam\enum\RoomSignupStatus;
use addons\exam\library\ValidateService;
use addons\exam\model\BaseModel;
use addons\exam\model\RoomGradeModel;
use addons\exam\model\RoomModel;
use addons\exam\model\RoomSignupModel;
use app\admin\model\exam\CateModel;
use app\admin\model\exam\SchoolModel;


/**
 * 考场接口
 */
class Room extends Base
{
    protected $noNeedLogin = ['index', 'detail'];
    protected $noNeedRight = ['*'];
    protected $user;

    /**
     * 查询出分类下的考场
     */
    public function index()
    {
        $cate_id    = input('cate_id', 0);
        $subject_id = input('subject_id', 0);
        $sort       = input('sort/s', '');

        $query = RoomModel::with(
            [
                'cates' => BaseModel::withSimpleCate(),
                'paper' => function ($query) {
                    $query->field('id, cate_id, title, mode, quantity, total_score, pass_score, limit_time');
                },
            ]
        )->where('status', CommonStatus::NORMAL);

        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }

        // 分类
        if ($cate_id && is_numeric($cate_id)) {
            $child_cate_ids = CateModel::getChildId($cate_id);
            array_push($child_cate_ids, $cate_id);
            $query->whereIn('cate_id', $child_cate_ids);
        }

        // 排序
        if ($sort && $sort != 'null') {
            $sort     = explode('|', $sort);
            $field    = $sort[0];
            $order_by = $sort[1];

            $field    = in_array($field, ['signup_count', 'grade_count', 'pass_count']) ? $field : 'signup_count';
            $order_by = $order_by == 'desc' ? 'desc' : 'asc';

            $query->order("{$field} $order_by");
        }

        $list = $query->order('weigh desc')->paginate(15, true);
        $this->success('', ['list' => $list]);
    }

    /**
     * 考场详情
     */
    public function detail()
    {
        if (!$room_id = input('room_id/d', '0')) {
            $this->error('缺少考场ID');
        }
        if (!$room = RoomModel::with(
            [
                'cates' => BaseModel::withSimpleCate(),
                'paper',
            ])
            ->where('id', $room_id)->find()
        ) {
            $this->error('考场信息不存在');
        }
        if ($room['status'] != CommonStatus::NORMAL) {
            $this->error('考场已关闭');
        }

        // 报名记录
        $signup_log = RoomSignupModel::where('room_id', $room_id)
            ->where('user_id', $this->auth->id)
            ->order('id desc')
            ->find();
        // 最后一次报名记录（非本次考场）
        $last_signup_log = RoomSignupModel::where('user_id', $this->auth->id)
            ->order('id desc')
            ->find();

        // 考试记录
        $room_grade_logs = $signup_log ? RoomGradeModel::where('room_id', $room_id)
            ->where('user_id', $this->auth->id)
            ->order('id desc')
            ->select() : [];

        // 符合开始考试条件（0：不允许，1：开始开始，2：开始补考）
        if ($signup_log) {
            // 已报名成功、在考试时间内
            $can_start = ($signup_log['status'] == RoomSignupStatus::ACCEPT && ($room['start_time'] < time() && $room['end_time'] > time())) ? 1 : 0;
            if ($can_start) {
                // 允许补考
                if ($room['is_makeup'] == 1) {
                    // 未超出补考次数限制
                    $room_grade_log_count = count($room_grade_logs);
                    if ($room_grade_log_count > 0) {
                        $can_start = $room_grade_log_count - 1 < $room['makeup_count'] ? 2 : 0;
                        if ($can_start) {
                            // 已有考试通过的记录，禁止补考
                            foreach ($room_grade_logs as $room_grade_log) {
                                if ($room_grade_log['is_pass']) {
                                    $can_start = 0;
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    // 非补考模式只能考一次
                    $can_start = count($room_grade_logs) == 0;
                }
            }
            $signup_log['can_start'] = $can_start;
        }

        $this->success('', [
            'room'        => $room,
            'signup_log'  => $signup_log,
            'exam_logs'   => $room_grade_logs,
            'last_signup' => $last_signup_log,
        ]);
    }

    /**
     * 考场报名
     */
    public function signup()
    {
        switch (true) {
            case !$room_id = input('room_id/d', '0'):
                $this->error('缺少考场ID');
            case !$real_name = input('real_name/s', ''):
                $this->error('请填写您的姓名');
            case !$phone = input('phone/s', ''):
                $this->error('请填写手机号码');
            case !ValidateService::phone($phone):
                $this->error('手机号码格式不正确');
            case !$room = RoomModel::get($room_id):
                $this->error('考场信息不存在');
            case $room['signup_mode'] == RoomMode::PASSWORD && $room['password'] != input('password/s', ''):
                $this->error('考场密码不正确');
            case $room['status'] != CommonStatus::NORMAL:
                $this->error('考场状态异常');
            case $room['start_time'] > time() || $room['end_time'] < time():
                $this->error('考场未开始或已过期');
            // case $room['signup_count'] >= $room['people_count']:
            //     $this->error('考场人数已满，无法报名参加考试');
        }

        if (!$school_id = input('school_id/d', '')) {
            $this->error('请选择您的学校');
        }
        if (!$class_name = input('class_name/s', '')) {
            $this->error('请填写您的班级');
        }
        $school = SchoolModel::where('id', $school_id)->where('status', GeneralStatus::NORMAL)->find();
        if (!$school) {
            $this->error('学校信息不存在');
        }

        // 已报名、被拒绝
        if ($signupLog = RoomSignupModel::where('room_id', $room_id)->where('user_id', $this->auth->id)->find()) {
            if ($signupLog->status != RoomSignupStatus::REJECT) {
                $this->error('该考场您已报过名了，请勿重复报名');
            }

            $signupLog->real_name = $real_name;
            $signupLog->phone     = $phone;
            $signupLog->status    = RoomSignupStatus::WAIT;

            if ($school_id) {
                $signupLog->school_id = $school_id;
            }
            if ($class_name) {
                $signupLog->class_name = $class_name;
            }

            if ($signupLog->save()) {
                $this->success('重新提交报名成功');
            }
        } else {
            // 非审核模式
            if ($room['signup_mode'] != RoomMode::AUDIT) {
                if ($room['people_count'] != 0 && $room['signup_count'] >= $room['people_count']) {
                    $this->error('考场人数已满，无法报名参加考试');
                }
            }

            // 创建报名记录
            if (RoomSignupModel::create([
                'user_id'    => $this->auth->id,
                'room_id'    => $room_id,
                'real_name'  => $real_name,
                'phone'      => $phone,
                'status'     => $room['signup_mode'] == RoomMode::AUDIT ? RoomSignupStatus::WAIT : RoomSignupStatus::ACCEPT,
                'school_id'  => $school_id,
                'class_name' => $class_name,
            ])) {
                // 非审核模式
                if ($room['signup_mode'] != RoomMode::AUDIT) {
                    // 递增报名成功人数
                    $room->setInc('signup_count');
                }

                $this->success($room['signup_mode'] == RoomMode::AUDIT ? '报名成功，请等待审核' : '报名成功');
            }
        }

        $this->error('报名失败，请重试');
    }
}
