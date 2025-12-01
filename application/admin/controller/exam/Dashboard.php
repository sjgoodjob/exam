<?php

namespace app\admin\controller\exam;

use addons\exam\enum\CommonStatus;
use addons\exam\enum\PaperPayStatus;
use addons\exam\enum\PayStatus;
use addons\exam\enum\RoomSignupStatus;
use addons\exam\model\UserInfoModel;
use addons\exam\model\UserModel;
use app\admin\model\exam\CateOrderModel;
use app\admin\model\exam\GradeModel;
use app\admin\model\exam\MemberOrderModel;
use app\admin\model\exam\PaperModel;
use app\admin\model\exam\PaperOrderModel;
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
        $start_time = Date::unixtime('day', -30);
        $end_time   = Date::unixtime('day', 0, 'end');
        // $user_join_list    = UserModel::where('createtime', 'between time', [$start_time, $end_time])
        //     ->field('createtime, status, COUNT(*) AS nums, DATE_FORMAT(FROM_UNIXTIME(createtime), "%Y-%m-%d") AS join_date')
        //     ->group('join_date')
        //     ->select();
        $paper_order_list  = PaperOrderModel::where('pay_time', 'between time', [$start_time, $end_time])
            ->where('status', PayStatus::PAID)
            ->field('pay_time, status, SUM(pay_money) as income, DATE_FORMAT(FROM_UNIXTIME(pay_time), "%Y-%m-%d") AS pay_date')
            ->group('pay_date')
            ->select();
        $member_order_list = MemberOrderModel::where('pay_time', 'between time', [$start_time, $end_time])
            ->where('status', PayStatus::PAID)
            ->field('pay_time, status, SUM(pay_money) as income, DATE_FORMAT(FROM_UNIXTIME(pay_time), "%Y-%m-%d") AS pay_date')
            ->group('pay_date')
            ->select();

        for ($time = $start_time; $time <= $end_time;) {
            $column[] = date("Y-m-d", $time);
            $time     += 86400;
        }

        // $user_list = array_fill_keys($column, 0);
        // foreach ($user_join_list as $v) {
        //     $user_list[$v['join_date']] = $v['nums'];
        // }

        $paper_income_list = array_fill_keys($column, 0);
        foreach ($paper_order_list as $v) {
            $paper_income_list[$v['pay_date']] = $v['income'];
        }

        $member_income_list = array_fill_keys($column, 0);
        foreach ($member_order_list as $v) {
            $member_income_list[$v['pay_date']] = $v['income'];
        }

        $today_time = Date::unixtime('day', 0, 'begin');
        $data       = [
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
            // 总会员开通人数
            'today_member_order_count'      => MemberOrderModel::where('status', PayStatus::PAID)->count(),
            // 总有效会员
            'total_member_count'            => UserInfoModel::where('expire_time', '>', $today_time)->count(),
            // 今日会员开通收入
            'today_member_pay_money'        => MemberOrderModel::where('pay_time', '>', $today_time)->sum('pay_money'),
            // 总会员开通收入
            'total_member_pay_money'        => MemberOrderModel::where('status', PayStatus::PAID)->sum('pay_money'),
            // 今日付费考试收入
            'today_paper_pay_money'         => PaperOrderModel::where('pay_time', '>', $today_time)->sum('pay_money'),
            // 总付费考试收入
            'total_paper_pay_money'         => PaperOrderModel::where('status', 'in', [PayStatus::PAID, PaperPayStatus::USED])->sum('pay_money'),
            // 今日付费题库收入
            'today_cate_pay_money'          => CateOrderModel::where('pay_time', '>', $today_time)->sum('pay_money'),
            // 总付费题库收入
            'total_cate_pay_money'          => CateOrderModel::where('status', PayStatus::PAID)->sum('pay_money'),
        ];

        $data['today_member_pay_money'] = $data['today_member_pay_money'] ?? 0;
        $data['total_member_pay_money'] = $data['total_member_pay_money'] ?? 0;
        $data['today_paper_pay_money']  = $data['today_paper_pay_money'] ?? 0;
        $data['total_paper_pay_money']  = $data['total_paper_pay_money'] ?? 0;
        $data['today_cate_pay_money']   = $data['today_cate_pay_money'] ?? 0;
        $data['total_cate_pay_money']   = $data['total_cate_pay_money'] ?? 0;

        // 总收入
        $data['today_income'] = $data['today_member_pay_money'] + $data['today_paper_pay_money'] + $data['today_cate_pay_money'];
        $data['total_income'] = $data['total_member_pay_money'] + $data['total_paper_pay_money'] + $data['total_cate_pay_money'];

        $this->view->assign($data);
        $this->assignconfig('column', array_keys($member_income_list));
        // $this->assignconfig('user_data', array_values($user_list));
        $this->assignconfig('paper_income_data', array_values($paper_income_list));
        $this->assignconfig('member_income_data', array_values($member_income_list));

        return $this->view->fetch();
    }

}
