<?php

namespace app\admin\model\exam;

use addons\exam\enum\RoomSignupStatus;
use addons\exam\model\BaseModel;
use addons\exam\model\RoomModel;
use app\admin\model\User;
use think\Db;


class RoomGradeModel extends BaseModel
{
    // 表名
    protected $name = 'exam_room_grade';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append
        = [
            'is_pass_text',
            'is_makeup_text',
            'grade_time_text',
        ];


    public function getIsPassList()
    {
        return ['0' => __('Is_pass 0'), '1' => __('Is_pass 1')];
    }

    public function getIsMakeupList()
    {
        return ['0' => __('Is_makeup 0'), '1' => __('Is_makeup 1')];
    }


    public function getIsPassTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_pass']) ? $data['is_pass'] : '');
        $list  = $this->getIsPassList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsMakeupTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_makeup']) ? $data['is_makeup'] : '');
        $list  = $this->getIsMakeupList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getGradeTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['grade_time']) ? $data['grade_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setGradeTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function belongsUser()
    {
        return $this->belongsTo(User::class);
    }

    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cate()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cates()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(RoomModel::class, 'room_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function signup()
    {
        return $this->belongsTo(RoomSignupModel::class, 'user_id', 'user_id', [])->where('status', RoomSignupStatus::ACCEPT);
    }

    public function signup1()
    {
        return $this->belongsTo(RoomSignupModel::class, 'user_id', 'user_id', [])->where('status', RoomSignupStatus::ACCEPT);
        // return $this->belongsTo(RoomSignupModel::class, 'user_id', 'user_id', [], 'LEFT')
        //     ->setEagerlyType(0)
        //     ->where('status', RoomSignupStatus::ACCEPT);
    }

    /**
     * 统计考场排名
     *
     * @param $room_id
     * @return array
     */
    public static function rankData($room_id)
    {
        if (!$room = RoomModel::get($room_id)) {
            exam_fail('考场信息不存在');
        }

        // +----------------------------------------------------------------------
        // 已统计排名
        // +----------------------------------------------------------------------
        // if ($room['is_rank']) {
        //     return [
        //         'summary' => [
        //             'grade_count' => $room['grade_count'],
        //             'pass_count'  => $room['pass_count'],
        //             'pass_rate'   => $room['pass_rate'],
        //             'cache_time'  => date('Y-m-d H:i:s', $room['updatetime']),
        //         ],
        //         'list'    => \addons\exam\model\RoomGradeModel::with(
        //             [
        //                 'user' => BaseModel::withSimpleUser(),
        //             ]
        //         )
        //             ->where('room_id', $room['id'])
        //             ->where('paper_id', $room['paper_id'])
        //             ->where('is_makeup', 0) // 补考不参与排名
        //             ->group('user_id')
        //             ->order('rank')
        //             ->limit(10)
        //             ->select(),
        //     ];
        // }

        // +----------------------------------------------------------------------
        // 未统计排名
        // +----------------------------------------------------------------------
        $grade_count = RoomGradeModel::where('room_id', $room['id'])->where('paper_id', $room['paper_id'])->group('user_id')->count();
        $pass_count  = RoomGradeModel::where('room_id', $room['id'])->where('is_pass', 1)->group('user_id')->count();
        $pass_rate   = round(($pass_count / $grade_count) * 100, 2) . '%';
        // $pass_rate   = bcmul(bcdiv($pass_count, $grade_count, 4), 100, 2);

        $list = !$grade_count ? [] : \addons\exam\model\RoomGradeModel::with(
            [
                // 'belongs_user' => BaseModel::withSimpleUser(),
                'user' => BaseModel::withSimpleUser(),
            ]
        )
            ->where('room_id', $room['id'])
            ->where('paper_id', $room['paper_id'])
            ->where('is_makeup', 0) // 补考不参与排名
            ->group('user_id')
            ->order('score desc, grade_time asc')
            ->select();

        // 保存数据，下次无需再次统计
        Db::transaction(function () use ($room, $grade_count, $pass_count, $pass_rate, &$list) {
            // 记录统计数据
            $room->grade_count = $grade_count;
            $room->pass_count  = $pass_count;
            $room->pass_rate   = $pass_rate;
            $room->is_rank     = 1;
            $room->save();

            // 记录排名数据
            foreach ($list as $index => &$grade) {
                $grade['rank'] = $index + 1;
                $grade->save();
            }
        });

        return [
            'summary' => [
                'grade_count' => $grade_count,
                'pass_count'  => $pass_count,
                'pass_rate'   => $pass_rate . '%',
                'cache_time'  => date('Y-m-d H:i:s'),
            ],
            // 只返回10条
            'list'    => count($list) > 10 ? array_slice($list, 0, 10) : $list,
        ];
    }
}
