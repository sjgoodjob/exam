<?php

namespace addons\exam\model;


use app\admin\model\exam\CateModel;

class GradeModel extends \app\admin\model\exam\GradeModel
{
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function cate()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id');
    }

    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id', 'id');
    }

    /**
     * 获取用户某日考试次数
     * @param int    $paper_id 试卷ID
     * @param int    $user_id  用户ID
     * @param string $date     日期
     * @return int|string
     */
    public static function getUserDateGradeCount($paper_id, $user_id, $date = '')
    {
        if (!$user_id) {
            return 0;
        }

        $date = $date ?: date('Y-m-d');
        return self::where('user_id', $user_id)
            ->where('paper_id', $paper_id)
            ->where('date', $date)
            ->count();
    }
}
