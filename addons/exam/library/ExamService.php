<?php
/**
 * Created by PhpStorm.
 * User         : zgcLives
 * CreateTime   : 2022/4/16 16:21
 */

namespace addons\exam\library;

use addons\exam\enum\CommonStatus;
use addons\exam\enum\PaperMode;
use addons\exam\enum\RoomSignupStatus;
use addons\exam\model\CateModel;
use addons\exam\model\CateUserLogModel;
use addons\exam\model\GradeModel;
use addons\exam\model\PaperModel;
use addons\exam\model\PaperOrderModel;
use addons\exam\model\QuestionModel;
use addons\exam\model\RoomGradeModel;
use addons\exam\model\RoomModel;
use addons\exam\model\RoomSignupModel;
use app\admin\model\exam\MaterialQuestionModel;

/**
 * 考试相关服务
 */
class ExamService
{
    /**
     * 获取试卷题目
     *
     * @param     $paper_id
     * @param int $room_id
     * @return array`
     */
    public static function getExamQuestion($paper_id, $room_id = 0)
    {
        if (!$paper_id) {
            exam_fail('缺少试卷ID');
        }

        $paper = self::validPaper($paper_id, $room_id);
        switch ($paper['mode']) {
            case PaperMode::RANDOM:
                $questions = self::getRandomQuestions($paper);
                break;
            case PaperMode::FIX:
                $questions = self::getFixQuestions($paper);
                break;
            default:
                exam_fail('试卷取题模式有误');
        }

        return [
            'paper'      => $paper,
            'questions'  => $questions,
            'start_time' => time(),
        ];
    }

    /**
     * 获取试卷随机题
     *
     * @param $paper
     * @return array
     */
    public static function getRandomQuestions($paper)
    {
        $configs   = $paper->configs;
        $questions = [];

        if (!isset($configs['cate_ids'])) {
            exam_fail('试卷随机取题配置有误');
        }

        foreach (QuestionModel::kindList as $kind) {
            if (!isset($configs[strtolower($kind)])) {
                continue;
            }

            $kind_config = $configs[strtolower($kind)];
            // 使用难度选题
            if ($kind_config['use_difficulty']) {

                foreach ($kind_config['difficulty'] as $difficulty => $value) {
                    if ($value['count']) {
                        $question  = QuestionModel::getListByCateAndKind($configs['cate_ids'], $kind, ['materialQuestions.question']);
                        $questions = array_merge(
                            $questions,
                            $question->where('difficulty', $difficulty)->limit($value['count'])->select()
                        // hidden_list_keys($question->where('difficulty', $difficulty)->limit($value['count'])->select(), ['answer', 'explain'])
                        );
                    }
                }

            } else {
                if ($kind_config['count']) {
                    $question = QuestionModel::getListByCateAndKind($configs['cate_ids'], $kind, ['materialQuestions.question']);
                    // dd(collection($question->limit($kind_config['count'])->select())->toArray());
                    $questions = array_merge(
                        $questions,
                        $question->limit($kind_config['count'])->select()
                    // hidden_list_keys($question->limit($kind_config['count'])->select(), ['answer', 'explain'])
                    );
                }
            }
        }

        if (count($questions) < intval($paper['quantity'])) {
            exam_fail('试卷题目数量不足，请联系管理员检查试卷配置');
        }

        // 合并材料题子题目
        $questions = QuestionModel::mergeMaterialQuestions($questions);

        return exam_hidden_list_keys($questions, ['answer', 'explain', 'origin_answer']);
    }

    /**
     * 获取试卷固定题
     *
     * @param $paper
     * @return array
     */
    public static function getFixQuestions($paper, $hidden = true)
    {
        $questions = QuestionModel::getFixListByPaper($paper['id'], ['materialQuestions.question']);
        if (count($questions) < intval($paper['quantity'])) {
            exam_fail('试卷题目数量不足，请联系管理员检查试卷配置');
        }

        // 合并材料题子题目
        $questions = QuestionModel::mergeMaterialQuestions($questions);
        if ($hidden) {
            return exam_hidden_list_keys($questions, ['answer', 'explain', 'origin_answer']);
        }
        return $questions;
    }

    /**
     * 试卷考试
     *
     * @param $user_id
     * @param $paper_id
     * @param $user_questions
     * @param $start_time
     * @param $paper
     * @return array
     */
    public static function paperExam($user_id, $paper_id, $user_questions, $start_time, &$paper, $room = null)
    {
        $from_room = $room ? true : false;
        $source    = $from_room ? 'ROOM' : 'PAPER';

        // 验证试卷
        $paper = self::validPaper($paper_id, $from_room ? 1 : 0);
        if (!$questions_ids = array_column($user_questions, 'id')) {
            exam_fail('提交的题目数据有误');
        }

        $paper_order = null;
        // 非考场考试
        if (!$from_room) {
            // 验证是否需要支付
            $check_result = PaperModel::checkPay($paper, $user_id, true);
            switch ($check_result['status']) {
                case 0:
                    exam_fail($check_result['msg']);
                case 2:
                    if (!$paper_order = PaperOrderModel::hasUsableOrder($paper_id, $user_id)) {
                        exam_fail('该试卷需要付费才能交卷，您尚未支付或订单已过期', ['need_pay' => true, 'pay_info' => $check_result]);
                    }
                    break;
            }

            // $cert_config_id = $paper['cert_config_id'] ?? 0;
        } else {
            // $cert_config_id = $room['cert_config_id'] ?? 0;
        }

        $answers      = array_column($user_questions, 'answer');
        $material_ids = array_column($user_questions, 'material_id');   // 材料题id
        $total_score  = 0;                                              // 试卷总分
        $error_count  = 0;                                              // 错误题目数量
        $error_ids    = [];                                             //错误题目id

        if ($paper['mode'] == PaperMode::RANDOM) {
            $questions = QuestionModel::whereIn('id', $questions_ids)->orderRaw("find_in_set(id, '" . implode(',', $questions_ids) . "')")->select();
        } else {
            $questions = self::getFixQuestions($paper, false);
        }

        // 材料题分数
        $material_score = [];
        foreach ($questions as $key => $question) {
            $score = 0;
            // 随机取题
            if ($paper['mode'] == PaperMode::RANDOM) {
                $kind       = $question['kind'];
                $difficulty = $question['difficulty'];

                // 属于材料题子题
                if (isset($material_ids[$key]) && $material_ids[$key]) {
                    if ($material_question = QuestionModel::where('id', $material_ids[$key])->cache(60)->find()) {
                        $kind       = 'MATERIAL';
                        $difficulty = $material_question['difficulty'];
                        // $score = PaperModel::getSingleScore($paper['configs'], strtolower($kind), strtolower($difficulty));    // 每题分数
                        // 材料题子题目设定的分数
                        $score = MaterialQuestionModel::where('parent_question_id', $material_ids[$key])
                            ->where('question_id', $question['id'])
                            ->cache(60)
                            ->value('score');
                    }
                } else {
                    $score = PaperModel::getSingleScore($paper['configs'], strtolower($kind), strtolower($difficulty));    // 每题分数
                }
            } else {
                // 固定取题
                $score = $question['score'];

                if ($question['id'] == 764) {
                    // dd([$score, $question, isset($material_ids[$key]), $material_ids[$key]]);
                }
            }

            switch ($question['kind']) {
                case 'JUDGE':   // 判断题
                case 'SINGLE':  // 单选题
                case 'MULTI':   // 多选题

                    // 答题正确
                    if (strtoupper($answers[$key]) == strtoupper($question['answer'])) {
                        $total_score                      += $score;
                        $user_questions[$key]['is_right'] = true;
                    } else {
                        array_push($error_ids, $question['id']);
                        $error_count++;
                        $user_questions[$key]['is_right'] = false;

                        // if ($user_id == 7503) {
                        //     if ($question['id'] == 745) {
                        //         // ddd($answers[$key]);
                        //     }
                        // }

                        // 记录错题
                        QuestionModel::recordWrong($question['kind'], $question['id'], $user_id, $answers[$key], $source, [
                            'cate_id'  => $question['cate_id'],
                            'paper_id' => $paper_id,
                            'room_id'  => $room['id'] ?? 0,
                        ]);
                        // $question->logWrong($user_id, $answers[$key]);
                    }
                    break;

                case 'FILL':    // 填空题
                    $user_answers       = $answers[$key];
                    $fill_right_count   = 0;
                    $question['answer'] = is_array($question['answer']) ? $question['answer'] : json_decode($question['answer'], true);
                    foreach ($question['answer'] as $fill_key => $fill_answer) {
                        foreach ($fill_answer['answers'] as $answer) {
                            if (isset($user_answers[$fill_key]) && exam_str_trim($user_answers[$fill_key]) == exam_str_trim($answer)) {
                                $fill_right_count++;
                                break;
                            }
                        }
                    }

                    // 所有填空项全对
                    if ($fill_right_count == count($question['answer'])) {
                        $user_questions[$key]['is_right'] = true;
                        $total_score                      += $score;
                    } else {
                        $user_questions[$key]['is_right'] = false;
                        array_push($error_ids, $question['id']);
                        $error_count++;

                        // 记录错题
                        QuestionModel::recordWrong($question['kind'], $question['id'], $user_id, $answers[$key], $source, [
                            'cate_id'  => $question['cate_id'],
                            'paper_id' => $paper_id,
                            'room_id'  => $room['id'] ?? 0,
                        ]);
                        // $question->logWrong($user_id, $answers[$key]);
                    }
                    break;

                case 'SHORT':   // 简答题
                    // 答案得分配置
                    $answer_config = is_string($question['answer']) ? json_decode($question['answer'], true) : $question['answer'];
                    $user_answers  = $answers[$key];
                    $right_score   = 0;
                    $answer_score  = [];
                    foreach ($answer_config['config'] as $answer_item) {
                        if ($right_score < $score) {
                            // 匹配答案关键词
                            if (strpos($user_answers, $answer_item['answer']) !== false) {
                                $right_score += $answer_item['score'];
                                // 得分情况
                                $answer_score[] = [
                                    'answer'        => $answer_item['answer'],
                                    'score'         => min($score, $answer_item['score']),
                                    'keyword_score' => $answer_item['score'],
                                    'max_score'     => $score,
                                ];
                            }
                        }
                    }

                    // 最高得分不能超过题目分数
                    $right_score = min($right_score, $score);

                    // 有得分
                    if ($right_score > 0) {
                        $user_questions[$key]['is_right'] = true;
                        $total_score                      += $right_score;
                    } else {
                        $user_questions[$key]['is_right'] = false;
                        array_push($error_ids, $question['id']);
                        $error_count++;

                        // 记录错题
                        QuestionModel::recordWrong($question['kind'], $question['id'], $user_id, $answers[$key], $source, [
                            'cate_id'  => $question['cate_id'],
                            'paper_id' => $paper_id,
                            'room_id'  => $room['id'] ?? 0,
                        ]);
                    }

                    $user_questions[$key]['answer_score'] = $answer_score;
                    break;
            }
        }

        // 递增参与人次
        $paper->setInc('join_count');

        // 支付订单设为已使用
        // if ($paper_order && $paper_order['expire_time'] == 0) {
        //     $paper_order->status = PaperPayStatus::USED;
        //     $paper_order->save();
        // }

        return [
            'total_score'  => $paper['total_score'],                                                                        // 试卷总分
            'score'        => $total_score,                                                                                 // 考试分数
            'is_pass'      => $total_score >= $paper['pass_score'],                                                         // 是否及格
            'pass_score'   => $paper['pass_score'],                                                                         // 及格分数
            'total_count'  => count($questions),                                                                            // 题目数量
            'right_count'  => count($questions) - $error_count,                                                             // 答对数量
            'error_count'  => $error_count,                                                                                 // 答错数量
            'start_time'   => $start_time,                                                                                  // 开始时间
            'grade_time'   => $paper['limit_time'] ? min(time() - $start_time, $paper['limit_time']) : time() - $start_time,// 考试用时
            'error_ids'    => implode(',', $error_ids),                                                                     // 错误题目id
            'question_ids' => implode(',', $questions_ids),                                                                 // 试题ID集合
            'user_answers' => json_encode($user_questions, JSON_UNESCAPED_UNICODE),                                         // 用户答案集合
            'configs'      => json_encode($paper['configs']),                                                               // 试卷配置
            'mode'         => $paper['mode'],                                                                               // 试卷选题模式
            // 'cert_config_id' => $cert_config_id,                                                                              // 证书配置id
            // 'room_id'        => $room['id'] ?? 0,                                                                      // 考场id
        ];
    }

    /**
     * 考场考试
     *
     * @param                     $user_id
     * @param                     $room_id
     * @param                     $room_grade_id
     * @param                     $questions
     * @param                     $start_time
     * @param                     $paper
     * @param                     $room
     * @param                     $is_makeup
     * @param RoomGradeModel|null $room_grade_log
     * @return array
     */
    public static function roomExam($user_id, $room_id, $room_grade_id, $questions, $start_time, &$paper, &$room, &$is_makeup, &$room_grade_log)
    {
        // 验证考场信息
        $room = self::validRoom($user_id, $room_id, $room_grade_id, $is_makeup, $room_grade_log);
        return self::paperExam($user_id, $room['paper_id'], $questions, $start_time, $paper, $room);
    }

    /**
     * 预创建考场考试记录（消耗一次考试记录，避免重复进入考场看题）
     *
     * @param $room_id
     * @param $user_id
     * @return int
     */
    public static function preRoomGrade($room_id, $user_id)
    {
        if (!$room_id) {
            return 0;
        }

        // 验证考场信息
        $room = self::validRoom($user_id, $room_id, 0, $is_makeup);

        // 创建考场考试记录
        $grade = RoomGradeModel::create([
            'user_id'     => $user_id,
            'room_id'     => $room_id,
            'cate_id'     => $room['cate_id'],
            'paper_id'    => $room['paper_id'],
            'score'       => 0,
            'is_pass'     => 0,
            'is_makeup'   => $is_makeup,
            'total_score' => $room['paper']['total_score'],
            'total_count' => $room['paper']['quantity'],
            'right_count' => 0,
            'error_count' => $room['paper']['quantity'],
            'rank'        => 0,
            'is_pre'      => 1,// 标记为预载入，提交成绩时须改为0
            'grade_time'  => 0,
        ]);

        return $grade['id'];
    }

    /**
     * 验证试卷
     *
     * @param int $paper_id 试卷ID
     * @param int $room_id  考场ID
     * @return PaperModel|null
     */
    public static function validPaper($paper_id, $room_id = 0)
    {
        $paper   = PaperModel::get($paper_id);
        $user_id = exam_getUserId();

        switch (true) {
            case !$paper:
                exam_fail('试卷信息不存在');
            case $paper->status != CommonStatus::NORMAL:
                exam_fail('试卷未开启');
            case $paper->mode == PaperMode::RANDOM && !$paper->configs:
                exam_fail('试卷未配置');
        }

        // 普通考试
        if (!$room_id) {
            if ($user_id && $paper['day_limit_count'] > 0 && GradeModel::getUserDateGradeCount($paper_id, $user_id) >= $paper['day_limit_count']) {
                exam_fail('当前试卷考试次数已达今日上限，明天再来吧~');
            }

            if ($paper['end_time'] > 0 && $paper['end_time'] < time()) {
                exam_fail('该试卷已失效，不能参与考试了');
            }

            if (!$cate = CateModel::get($paper['cate_id'])) {
                exam_fail('试卷所属题库信息不存在');
            }

            if (!$cate['is_free'] && $cate['price'] > 0) {
                if (!CateUserLogModel::isOpenCate($user_id, $cate['id'])) {
                    exam_fail('该题库需要付费开通，请先购买后再试', ['need_open' => true, 'cate' => $cate]);
                }
            }
        }

        return $paper;
    }

    /**
     * 验证考场
     *
     * @param int                 $user_id        考试用户
     * @param int                 $room_id        试卷ID
     * @param int                 $room_grade_id  考场预创建成绩ID
     * @param int                 $is_makeup      返回是否是补考
     * @param RoomGradeModel|null $room_grade_log 预创建的成绩记录
     * @return RoomModel|null
     */
    private static function validRoom($user_id, $room_id, $room_grade_id, &$is_makeup, &$room_grade_log = null)
    {
        $room = RoomModel::get($room_id);

        switch (true) {
            case !$room:
                exam_fail('考场信息不存在');
            case $room['status'] != CommonStatus::NORMAL:
                exam_fail('考场未开启');
            case time() < $room['start_time'] || time() > $room['end_time']:
                exam_fail('考场时间未开始或已结束');
            case !$roomSignup = RoomSignupModel::where('room_id', $room_id)->where('user_id', $user_id)->find():
                exam_fail('您尚未报名此考场');
            case $roomSignup['status'] != RoomSignupStatus::ACCEPT:
                exam_fail('您的考场报名信息状态有误');
        }

        // 考场允许补考
        if ($room['is_makeup'] == 1 && $room['makeup_count'] > 0) {
            // $query = RoomGradeModel::where('room_id', $room_id)->where('paper_id', $room['paper_id'])->where('user_id', $user_id);
            // 考试次数
            $room_exam_count = RoomGradeModel::where('room_id', $room_id)
                ->where('paper_id', $room['paper_id'])
                ->where('user_id', $user_id)
                ->where('is_pre', 0)
                ->count();
            // 补考次数
            $makeup_count     = RoomGradeModel::where('room_id', $room_id)
                ->where('paper_id', $room['paper_id'])
                ->where('user_id', $user_id)
                ->where('is_makeup', 1)
                ->count();
            $min_makeup_count = $makeup_count - ($room_grade_id ? 1 : 0);
            if ($min_makeup_count > $room['makeup_count']) {
                exam_fail("您已超过本考场的补考次数，无法继续考试");
            }

            $last_exam_log = RoomGradeModel::where('room_id', $room_id)
                ->where('paper_id', $room['paper_id'])
                ->where('user_id', $user_id)
                ->order('id desc')
                ->find();
            if ($last_exam_log && $last_exam_log['is_pass'] != 0) {
                exam_fail('最后一次考试已及格，不需要补考了');
            }

            // 考试次数大于0视为补考
            $is_makeup = $room_exam_count >= 1 ? 1 : 0;

            if ($user_id == 5134) {
                // ddd($room_exam_count, $makeup_count, $min_makeup_count, $is_makeup);
            }
        } else {
            if (RoomGradeModel::where('room_id', $room_id)->where('user_id', $user_id)->where('is_pre', 0)->count() > 0) {
                exam_fail('您已参加过该考场考试了');
            }

            $is_makeup = 0;
        }

        // 考场预创建记录验证
        if ($room_grade_id) {
            if (!$room_grade_log = RoomGradeModel::where('id', $room_grade_id)->where('user_id', $user_id)->find()) {
                exam_fail('考场成绩错误');
            } else if ($room_grade_log['is_pre'] == 0) {
                exam_fail('本次考场考试已提交过成绩了，请勿重复提交');
            }
        }

        return $room;
    }
}
