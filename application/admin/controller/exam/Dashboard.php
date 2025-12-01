<?php

namespace app\admin\controller\exam;

use addons\exam\enum\CommonStatus;
use addons\exam\enum\RoomSignupStatus;
use addons\exam\model\UserModel;
use app\admin\model\exam\GradeModel;
use app\admin\model\exam\PaperModel;
use app\admin\model\exam\QuestionModel;
use app\admin\model\exam\RoomGradeModel;
use app\admin\model\exam\RoomModel;
use app\admin\model\exam\RoomSignupModel;
use app\common\controller\Backend;
use fast\Date;

/**
 * 控制台
 * @icon   fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        try {
            \think\Db::execute("SET @@sql_mode='';");
        } catch (\Exception $e) {

        }

        $column     = [];
        $start_time = Date::unixtime('day', -6);
        $end_time   = Date::unixtime('day', 0, 'end');
        $join_list  = UserModel::where('jointime', 'between time', [$start_time, $end_time])
            ->field('jointime, status, COUNT(*) AS nums, DATE_FORMAT(FROM_UNIXTIME(jointime), "%Y-%m-%d") AS join_date')
            ->group('join_date')
            ->select();

        for ($time = $start_time; $time <= $end_time;) {
            $column[] = date("Y-m-d", $time);
            $time     += 86400;
        }
        $user_list = array_fill_keys($column, 0);
        foreach ($join_list as $k => $v) {
            $user_list[$v['join_date']] = $v['nums'];
        }

        $today_time = Date::unixtime('day', 0, 'begin');
        $this->view->assign([
            // 用户总数
            'total_user_count'              => UserModel::count(),
            // 今日新增用户数
            'today_user_count'              => UserModel::where('createtime', '>', $today_time)->count(),
            // 总题数
            'total_question_count'          => QuestionModel::where('status', CommonStatus::NORMAL)->count(),
            // 总试卷数
            'total_paper_count'             => PaperModel::where('status', CommonStatus::NORMAL)->count(),
            // 总考场数
            'total_room_count'              => RoomModel::where('status', CommonStatus::NORMAL)->count(),
            // 总参与考试次数
            'total_exam_user_count'         => GradeModel::count(),
            // 今日参与考试次数
            'today_exam_user_count'         => GradeModel::where('createtime', '>', $today_time)->count(),
            // 总参与考场考试次数
            'total_room_user_count'         => RoomGradeModel::count(),
            // 今日参与考场考试次数
            'today_room_user_count'         => RoomGradeModel::where('createtime', '>', $today_time)->count(),
            // 待处理报名数量
            'total_wait_apply_signup_count' => RoomSignupModel::where('status', RoomSignupStatus::WAIT)->count(),
        ]);

        $this->assignconfig('column', array_keys($user_list));
        $this->assignconfig('user_data', array_values($user_list));

        return $this->view->fetch();
    }

}
