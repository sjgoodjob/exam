<?php

namespace addons\exam\model;


use app\admin\model\exam\CateModel;
use app\admin\model\exam\SchoolModel;
use think\Db;

class RoomGradeModel extends \app\admin\model\exam\RoomGradeModel
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

    public function room()
    {
        return $this->belongsTo(RoomModel::class, 'room_id', 'id');
    }

    public function school()
    {
        return $this->belongsTo(SchoolModel::class, 'school_id', 'id');
    }

    public static function getRank($room_id)
    {

    }

    /**
     * 获取试卷参与人员
     * @param $room_id
     * @param $slice
     * @return array
     */
    public static function getJoinUsers($room_id, $slice = 0)
    {
        $user_ids = Db::name('exam_room_grade')->where('room_id', $room_id)->group('user_id')->column('user_id');
        if ($user_ids) {
            // 截取数组
            $user_ids = $slice ? array_slice($user_ids, $slice) : $user_ids;
            return Db::name('user')->whereIn('id', $user_ids)->select();
        }

        return [];
    }
}
