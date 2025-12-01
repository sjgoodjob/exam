<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;
use traits\model\SoftDelete;


class PaperModel extends BaseModel
{
    use SoftDelete;

    // 表名
    protected $name = 'exam_paper';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append
        = [
            'mode_text',
            'status_text',
            'start_time_text',
            'end_time_text',
        ];


    public function getModeList()
    {
        return ['RANDOM' => __('Random'), 'FIX' => __('Fix')];
    }

    public function getKindList()
    {
        return ['RANDOM' => __('Random'), 'FIX' => __('Fix')];
    }

    public function getStatusList()
    {
        return ['NORMAL' => __('Normal'), 'HIDDEN' => __('Hidden')];
    }


    public function getModeTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['mode'] ?? '');
        $list  = $this->getModeList();
        return $list[$value] ?? '';
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['status'] ?? '');
        $list  = $this->getStatusList();
        return $list[$value] ?? '';
    }

    public function getStartTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['start_time']) ? $data['start_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['end_time']) ? $data['end_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setStartTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function cate()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cates()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id');
    }

    /**
     * 试卷不同类型或难度题的分数
     *
     * @param $configs
     * @param $kind
     * @param $difficulty
     * @return mixed
     */
    public static function getSingleScore($configs, $kind, $difficulty)
    {
        $config = $configs[$kind];

        if ($config['use_difficulty'] && !empty($config['difficulty'][$difficulty]) && !empty($config['difficulty'][$difficulty]['score'])) {
            return $config['difficulty'][$difficulty]['score'];
        }

        return $config['score'];
    }

    /**
     * 获取试卷考试成绩数量
     *
     * @param $paper_id
     * @return int|string
     * @throws \think\Exception
     */
    public static function getGradeCount($paper_id)
    {
        if (!$paper_id) {
            return 0;
        }

        return GradeModel::where('paper_id', $paper_id)->count();
    }

    /**
     * 获取试卷考场考试成绩数量
     *
     * @param $paper_id
     * @return int|string
     * @throws \think\Exception
     */
    public static function getRoomGradeCount($paper_id)
    {
        if (!$paper_id) {
            return 0;
        }

        return RoomGradeModel::where('paper_id', $paper_id)->count();
    }
}
