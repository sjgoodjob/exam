<?php

namespace addons\exam\controller;

use addons\exam\enum\CommonStatus;
use addons\exam\enum\ExamMode;
use addons\exam\enum\UserScoreType;
use addons\exam\library\CertService;
use addons\exam\library\ExamService;
use addons\exam\library\ScoreService;
use addons\exam\model\CateModel;
use addons\exam\model\GradeModel;
use addons\exam\model\PaperModel;
use addons\exam\model\PaperOrderModel;
use addons\exam\model\QuestionModel;
use addons\exam\model\RoomSignupModel;
use addons\exam\model\UserModel;
use think\Request;


/**
 * 试卷接口
 */
class Paper extends Base
{
    protected $noNeedLogin = ['index'];
    protected $noNeedRight = ['*'];
    protected $user;

    /**
     * 查询出分类下的试卷
     */
    public function index()
    {
        $subject_id = input('subject_id/d', '0');
        $cate_id    = input('cate_id/d', '0');
        $sort       = input('sort/s', '');
        $now        = time();

        $query = PaperModel::with([
            'cates' => function ($query) {
                $query->withField('id, name');
            },
        ])
            ->where('status', CommonStatus::NORMAL)
            ->where('is_only_room', 0)// 过滤仅考场使用的试卷
            ->whereRaw("((start_time = 0 and end_time = 0) or (start_time < {$now} and end_time > {$now}))");

        // 分类
        if ($cate_id) {
            $child_cate_ids = CateModel::getChildId($cate_id);
            array_push($child_cate_ids, $cate_id);
            $query->whereIn('cate_id', $child_cate_ids);
        }

        // 科目
        if ($subject_id) {
            $query->where('subject_id', $subject_id);
        }

        // 排序
        if ($sort && $sort != 'null') {
            $sort     = explode('|', $sort);
            $field    = $sort[0];
            $order_by = $sort[1];

            $field    = in_array($field, ['join_count']) ? $field : 'join_count';
            $order_by = $order_by == 'desc' ? 'desc' : 'asc';

            $query->order("{$field} $order_by");
        }

        $list = $query->paginate(15, true);
        $this->success('', ['list' => $list]);
    }

    /**
     * 试卷取题接口
     */
    public function getExamQuestion()
    {
        $paper_id = input('paper_id/d', 0);
        $room_id  = input('room_id/d', 0);

        // 验证是否需要绑定手机号
        UserModel::isMustBindMobile($this->auth->getUser());

        // 预创建考场考试记录
        $room_grade_id = ExamService::preRoomGrade($room_id, $this->auth->id);

        // 获取试卷题目
        $question_data = ExamService::getExamQuestion($paper_id, $room_id);

        // 标记题目是否已收藏
        $question_data['questions'] = QuestionModel::isCollected($this->auth->id, $question_data['questions']);

        $this->success('', array_merge($question_data, ['room_grade_id' => $room_grade_id]));
    }

    /**
     * 交卷
     */
    public function submit()
    {
        $request       = Request::instance();
        $user_id       = $this->auth->id;
        $paper_id      = $request->post('paper_id/d', 0);
        $questions     = $request->post('questions/a', []);
        $start_time    = $request->post('start_time/d', time());
        $room_id       = $request->post('room_id/d', 0);
        $room_grade_id = $request->post('room_grade_id/d', 0);

        if (!$user_id || !$paper_id || !$questions) {
            $this->error('提交数据有误' . $user_id);
        }

        // 防重复提交
        exam_antiRepeat("paper_submit-{$paper_id}-{$room_id}-{$user_id}", 2);

        // 考场考试
        if ($room_id) {
            $is_makeup = 0;
            $result    = ExamService::roomExam($user_id, $room_id, $room_grade_id, $questions, $start_time, $paper, $room, $is_makeup, $room_grade_log);

            $sign_up   = RoomSignupModel::where('room_id', $room_id)->where('user_id', $user_id)->find();
            $user_name = $sign_up['real_name'] ?? '';
            // 成绩写入报名时的学校、班级
            $result['school_id']  = $sign_up['school_id'] ?? 0;
            $result['class_name'] = $sign_up['class_name'] ?? '';

            if (!empty($room['cert_config_id'])) {
                // 生成证书
                $cert = CertService::createCert($room['cert_config_id'], $room_id, $paper_id, $user_id, $user_name, $result['score']);
                if ($cert) {
                    $result['cert'] = $cert;
                } else {
                    $result['cert'] = '';
                }
            }

            // if ($user_id == 5134) {
            //     ddd($is_makeup);
            // }
            // 记录考场考试成绩
            $room_grade_log->allowField(true)->save(
                array_merge(
                    $result,
                    [
                        // 'cate_id'   => $paper['cate_id'],
                        'user_id'   => $user_id,
                        'paper_id'  => $paper_id,
                        'is_makeup' => $is_makeup,
                        'is_pre'    => 0, // 提交成绩后不再为预创建标记
                    ],
                    [
                        'exam_mode' => ExamMode::ROOM,
                    ]
                )
            );

            $result['grade_id'] = $room_grade_log['id'];

            // 试卷考试得积分
            $result['point'] = [
                'get_point' => ScoreService::getScore($this->auth->id, UserScoreType::ROOM, $room_grade_log),
                'type'      => UserScoreType::getDescription(UserScoreType::ROOM),
            ];
        } else {
            $result = ExamService::paperExam($user_id, $paper_id, $questions, $start_time, $paper);

            // 记录考试成绩
            $paper_grade_log = GradeModel::create(array_merge(
                $result,
                [
                    'cate_id'  => $paper['cate_id'],
                    'user_id'  => $user_id,
                    'paper_id' => $paper_id,
                ],
                [
                    // 'exam_mode' => ExamMode::PAPER,
                    'date' => date('Y-m-d'),
                ]), true);

            $result['grade_id'] = $paper_grade_log['id'];

            // 试卷考试得积分
            $result['point'] = [
                'get_point' => ScoreService::getScore($this->auth->id, UserScoreType::PAPER, $paper_grade_log),
                'type'      => UserScoreType::getDescription(UserScoreType::PAPER),
            ];
        }
        return json($result);
    }

    /*
     * 查看错题
     * Robin
     * @param $ids
     */
    public function error_ids($ids)
    {
        $questions = QuestionModel::whereIn('id', ($ids))->select();
        $this->success('', $questions);
    }

    /**
     * 检查是否需要支付
     */
    public function checkPay()
    {
        $paper_id = input('paper_id/d', 0);
        $room_id  = input('room_id/d', 0);

        if (!$paper_id) {
            $this->error('考试试卷参数有误');
        }
        if (!$paper = PaperModel::get($paper_id)) {
            $this->error('考试试卷数据不存在');
        }
        if ($room_id) {
            $this->success('', ['status' => 1]);
        }

        // 检测题库是否需要付费
        // $result = CateModel::checkPay($paper['cate_id'], $this->auth->id);
        // if ($result['status'] == 2) {
        //     // 需要支付直接返回
        //     $this->success('', $result);
        // }
        // dd($result);

        // 检测试卷是否需要付费
        $result = PaperModel::checkPay($paper, $this->auth->id, false, $room_id);
        $this->success('', $result);
    }

    /**
     * 创建考试订单
     */
    public function createOrder()
    {
        $paper_id = input('paper_id/d', 0);
        $room_id  = input('room_id/d', 0);

        if (!$paper_id) {
            $this->error('考试试卷参数有误');
        }
        if (!$paper = PaperModel::get($paper_id)) {
            $this->error('考试试卷数据不存在');
        }

        $result = PaperModel::checkPay($paper, $this->auth->id, false, $room_id);
        if ($result['status'] != 2) {
            $this->error('该考试试卷无须付费，请尝试重新进入');
        }

        if (PaperOrderModel::hasUsableOrder($paper_id, $this->auth->id)) {
            $this->error('您已付费，无须重复支付，请尝试重新进入');
        }

        $expire_time = $paper['pay_effect_days'] ? time() + $paper['pay_effect_days'] * 86400 : 0;
        $order       = PaperOrderModel::createOrder($paper['id'], $this->auth->id, $result['is_member'] ? $paper['member_price'] : $paper['price'], $expire_time);
        $payment     = PaperOrderModel::createPayment($this->auth->id, $order['order_no'], $order['amount']);

        $this->success('', [
            'order'   => $order,
            'payment' => $payment,
        ]);
    }
}
