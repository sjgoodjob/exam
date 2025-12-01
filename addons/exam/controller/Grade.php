<?php

namespace addons\exam\controller;

use addons\exam\model\BaseModel;
use addons\exam\model\GradeModel;
use think\Db;


/**
 * 试卷考试成绩接口
 */
class Grade extends Base
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 获取成绩列表
     */
    public function index()
    {
        $list = GradeModel::with(
            [
                // 'user'  => BaseModel::withSimpleUser(),
                'cate'  => BaseModel::withSimpleCate(),
                'paper' => BaseModel::withSimplePaper(),
            ]
        )
            ->where('user_id', $this->auth->id)
            ->order('id desc')
            ->paginate(15, true);

        $this->success('', compact('list'));
    }

    /**
     * 排行榜
     */
    public function rank()
    {
        if (!$paper_id = input('paper_id/d', '0')) {
            $this->error('缺少试卷信息');
        }

        $result = exam_cache_data("rank:paper-{$paper_id}", function () use ($paper_id) {
            $grade_count = GradeModel::where('paper_id', $paper_id)->group('user_id')->count();
            $pass_count  = GradeModel::where('paper_id', $paper_id)->where('is_pass', 1)->group('user_id')->count();
            $pass_rate   = round(($pass_count / $grade_count) * 100, 2) . '%';
            // $pass_rate   = bcmul(bcdiv($pass_count, $grade_count, 4), 100, 2) . '%';

            if ($grade_count) {
                // 子查询，先取出最新的成绩
                $subQuery = Db::name('exam_grade')
                    ->field('id,user_id,cate_id,paper_id,mode,MAX(score) AS score,is_pass,grade_time')
                    ->where('paper_id', $paper_id)
                    ->group('user_id')
                    ->order('id desc')
                    ->buildSql();

                // 再根据成绩、考试时间排序，取出前10名
                $list = GradeModel::with(
                    [
                        'user' => BaseModel::withSimpleUser(),
                    ]
                )->table($subQuery . ' exam_grade')
                    // ->where('paper_id', $paper_id)
                    // ->group('user_id')
                    ->order('score desc, grade_time asc')
                    ->limit(10)
                    ->select();
            } else {
                $list = [];
            }

            return [
                'summary' => [
                    'grade_count' => $grade_count,
                    'pass_count'  => $pass_count,
                    'pass_rate'   => $pass_rate,
                    'cache_time'  => datetime(time()),
                ],
                'list'    => $list,
            ];
        }, 3600, true);

        $this->success('', json_decode($result, true));
    }
}
