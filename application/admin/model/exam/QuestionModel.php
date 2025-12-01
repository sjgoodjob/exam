<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;
use think\Collection;
use think\Db;
use think\Model;
use traits\model\SoftDelete;
use addons\exam\enum\CommonStatus;


class QuestionModel extends BaseModel
{
    use SoftDelete;

    // 表名
    protected $name = 'exam_question';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append
        = [
            'kind_text',
            'difficulty_text',
            'status_text',
            'title_video_url',
            'explain_video_url',
        ];

    const kindList       = ['JUDGE', 'SINGLE', 'MULTI', 'FILL', 'SHORT', 'MATERIAL'];
    const difficultyList = ['EASY', 'GENERAL', 'HARD'];
    const statusList     = ['NORMAL', 'HIDDEN'];

    public function getKindList()
    {
        return [
            'JUDGE'    => '判断题',
            'SINGLE'   => '单选题',
            'MULTI'    => '多选题',
            'FILL'     => '填空题',
            'SHORT'    => '简答题',
            'MATERIAL' => '材料题',
        ];
    }

    public function getDifficultyList()
    {
        return ['EASY' => '简单', 'GENERAL' => '普通', 'HARD' => '困难'];
    }

    public function getStatusList()
    {
        return ['NORMAL' => '正常', 'HIDDEN' => '隐藏'];
    }


    public function getKindTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['kind'] ?? '');
        $list  = $this->getKindList();
        return $list[$value] ?? '';
    }


    public function getDifficultyTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['difficulty'] ?? '');
        $list  = $this->getDifficultyList();
        return $list[$value] ?? '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['status'] ?? '');
        $list  = $this->getStatusList();
        return $list[$value] ?? '';
    }

    public function getTitleVideoUrlAttr($value, $data)
    {
        $value = $data['title_video'] ?? '';
        return cdnurl($value, true);
    }

    public function getExplainVideoUrlAttr($value, $data)
    {
        $value = $data['explain_video'] ?? '';
        return cdnurl($value, true);
    }

    public function cate()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cates()
    {
        return $this->belongsTo(CateModel::class, 'cate_id');
    }

    /**
     * 材料题子题目
     *
     * @return \think\model\relation\HasMany
     */
    public function materialQuestions()
    {
        return $this->hasMany(MaterialQuestionModel::class, 'parent_question_id', 'id')->order('weigh');
    }

    public function materialParent()
    {
        return $this->belongsTo(self::class, 'material_question_id', 'id');
    }

    protected function scopeCate($query, $cate_ids)
    {
        $query->whereIn('cate_id', $cate_ids);
    }

    protected function scopeKind($query, $kind, $limit = 0)
    {
        $query->where('kind', $kind);

        if ($limit) {
            $query->limit($limit);
        }
    }

    protected function scopeDifficulty($query, $difficulty, $limit = 0)
    {
        $query->where('difficulty', $difficulty);
    }

    /**
     * 获取多题库下各类题型的不同难度题数
     *
     * @param $cate_ids
     * @return array|bool|\PDOStatement|string|Model|null
     */
    public function getCount($cate_ids)
    {
        return Db::name($this->name)
            ->whereIn('cate_id', $cate_ids)
            ->where('is_material_child', 0) // 材料题子题不计入总数
            ->where('status', 'NORMAL')
            ->whereNull('deletetime')
            ->field("
                COUNT(id) as 'total', 
                
                COUNT(CASE WHEN kind = 'JUDGE' then 1 END) as 'judge',
                COUNT(CASE WHEN kind = 'JUDGE' and difficulty = 'EASY' then 1 END) as 'judge_easy',
                COUNT(CASE WHEN kind = 'JUDGE' and difficulty = 'GENERAL' then 1 END) as 'judge_general',
                COUNT(CASE WHEN kind = 'JUDGE' and difficulty = 'HARD' then 1 END) as 'judge_hard',
                
                COUNT(CASE WHEN kind = 'SINGLE' then 1 END) as 'single',
                COUNT(CASE WHEN kind = 'SINGLE' and difficulty = 'EASY' then 1 END) as 'single_easy',
                COUNT(CASE WHEN kind = 'SINGLE' and difficulty = 'GENERAL' then 1 END) as 'single_general',
                COUNT(CASE WHEN kind = 'SINGLE' and difficulty = 'HARD' then 1 END) as 'single_hard',
                
                COUNT(CASE WHEN kind = 'MULTI' then 1 END) as 'multi',
                COUNT(CASE WHEN kind = 'MULTI' and difficulty = 'EASY' then 1 END) as 'multi_easy',
                COUNT(CASE WHEN kind = 'MULTI' and difficulty = 'GENERAL' then 1 END) as 'multi_general',
                COUNT(CASE WHEN kind = 'MULTI' and difficulty = 'HARD' then 1 END) as 'multi_hard',
                
                COUNT(CASE WHEN kind = 'FILL' then 1 END) as 'fill',
                COUNT(CASE WHEN kind = 'FILL' and difficulty = 'EASY' then 1 END) as 'fill_easy',
                COUNT(CASE WHEN kind = 'FILL' and difficulty = 'GENERAL' then 1 END) as 'fill_general',
                COUNT(CASE WHEN kind = 'FILL' and difficulty = 'HARD' then 1 END) as 'fill_hard',
                
                COUNT(CASE WHEN kind = 'SHORT' then 1 END) as 'short',
                COUNT(CASE WHEN kind = 'SHORT' and difficulty = 'EASY' then 1 END) as 'short_easy',
                COUNT(CASE WHEN kind = 'SHORT' and difficulty = 'GENERAL' then 1 END) as 'short_general',
                COUNT(CASE WHEN kind = 'SHORT' and difficulty = 'HARD' then 1 END) as 'short_hard',
                
                COUNT(CASE WHEN kind = 'MATERIAL' then 1 END) as 'material',
                COUNT(CASE WHEN kind = 'MATERIAL' and difficulty = 'EASY' then 1 END) as 'material_easy',
                COUNT(CASE WHEN kind = 'MATERIAL' and difficulty = 'GENERAL' then 1 END) as 'material_general',
                COUNT(CASE WHEN kind = 'MATERIAL' and difficulty = 'HARD' then 1 END) as 'material_hard'
                
            ")->find();

    }

    /*
     * 根据关键词模糊查询10条题目
     * Robin
     * */
    // public function getList($params)
    // {
    //     $param = array_merge([
    //         'keyword'  => '',         // 搜索关键词
    //         'sortType' => '',         // 排序类型
    //         'listRows' => 20,         // 每页数量
    //         'sortRand' => 0           //是否随机查询，0 or 1
    //     ], $params);
    //
    //     if ($param['keyword'])
    //         $this->where('title', 'like', '%' . $param['keyword'] . '%');
    //
    //     if ($param['sortType'])
    //         $this->order($param['sortType']);
    //
    //     if ($param['sortRand'])
    //         $this->orderRaw('rand()');
    //
    //     return $this->with('collected')->where('cate_id', intval($param['cate_id']))
    //         ->where('status', 'NORMAL')
    //         ->paginate($param['listRows'], true);
    // }

    /**
     * 获取题目列表
     *
     * @param string $cates  分类ID，多个逗号隔开
     * @param string $kind   题型
     * @param array  $with   关联模型
     * @param string $status 状态
     * @return QuestionModel
     */
    public static function getListByCateAndKind($cates, $kind, $with = [], $status = 'NORMAL')
    {
        $where = [
            'cate_id'           => ['in', $cates],
            'kind'              => $kind,
            'is_material_child' => 0,// 材料题子题不显示
        ];

        if ($status) {
            $where['status'] = ['=', $status];
        }

        return self::with($with)
            ->where($where)
            ->orderRaw('rand()');
    }

    /**
     * 获取试卷固定题目
     *
     * @param int    $paper_id 试卷ID
     * @param array  $with     关联模型
     * @param string $status   状态
     * @return bool|\PDOStatement|string|Collection
     */
    public static function getFixListByPaper($paper_id, $with = [], $status = CommonStatus::NORMAL)
    {
        $query = self::with($with)
            ->alias('question_model')
            ->join('exam_paper_question pq', 'question_model.id = pq.question_id')
            ->where('pq.paper_id', $paper_id)
            ->where('is_material_child', 0)
            ->field('question_model.*,pq.question_id,pq.score,pq.sort,pq.answer_config')
            ->order('pq.sort', 'desc');

        if ($status) {
            $query->where('question_model.status', $status);
        }

        $questions = $query->select();

        foreach ($questions as &$question) {
            if (!empty($question['answer_config'])) {
                // 简答题 - 替换答案分数配置
                if ($question['kind'] == 'SHORT') {
                    $question['answer'] = $question['answer_config'];
                }
            }
        }

        return $questions;
    }

    /**
     * 记录错题
     */
    public function logWrong($user_id, $user_answer = null)
    {
        // if ($item = QuestionWrongModel::where('user_id', $user_id)
        //     ->where('question_id', $this->id)
        //     ->find()) {
        //     $item->user_answer = $user_answer;
        //     $item->save();
        //
        //     return $item;
        // } else {
        //     return QuestionWrongModel::create([
        //         'user_id'     => $user_id,
        //         'question_id' => $this->id,
        //         'user_answer' => $user_answer,
        //     ]);
        // }

        if (is_array($user_answer)) {
            $user_answer = json_encode($user_answer, JSON_UNESCAPED_UNICODE);
        } else if (is_string($user_answer)) {
            $user_answer = trim($user_answer);
            if (strpos($user_answer, ',')) {
                $user_answer = json_encode(explode(',', $user_answer), JSON_UNESCAPED_UNICODE);
            }
        } else {
            $user_answer = null;
        }

        return QuestionWrongModel::updateOrCreate(
            [
                'user_id'     => $user_id,
                'question_id' => $this->id,
            ],
            [
                'user_id'     => $user_id,
                'question_id' => $this->id,
                'user_answer' => $user_answer, //is_array($user_answer) ? json_encode($user_answer, JSON_UNESCAPED_UNICODE) : $user_answer,
            ]
        );
    }

    /**
     * 记录错题
     *
     * @param string $question_kind 题型
     * @param int    $question_id   题目ID
     * @param int    $user_id       用户ID
     * @param null   $user_answer   用户答案
     * @param string $source        来源：PAPER=试卷，ROOM=考场，TRAINING=练题
     * @return mixed
     */
    public static function recordWrong($question_kind, $question_id, $user_id, $user_answer = null, $source = 'PAPER', $source_data = [])
    {
        if (is_array($user_answer)) {
            $user_answer = json_encode($user_answer, JSON_UNESCAPED_UNICODE);
        } else if (is_string($user_answer)) {
            $user_answer = trim($user_answer);
            if (in_array($question_kind, ['JUDGE', 'SINGLE', 'MULTI'])) {
                $user_answer = strtoupper($user_answer);
            } else if (strpos($user_answer, ',')) {
                $user_answer = json_encode(explode(',', $user_answer), JSON_UNESCAPED_UNICODE);
            }
        } else {
            $user_answer = null;
        }

        // 按每次错题记录
        return QuestionWrongModel::create(
            [
                'user_id'     => $user_id,
                'question_id' => $question_id,
                'user_answer' => $user_answer,
                'kind'        => $source,
                'cate_id'     => $source_data['cate_id'] ?? 0,
                'paper_id'    => $source_data['paper_id'] ?? 0,
                'room_id'     => $source_data['room_id'] ?? 0,
            ]
        );
        // 相同题目仅记录最后一次错题
        // return QuestionWrongModel::updateOrCreate(
        //     [
        //         'user_id'     => $user_id,
        //         'question_id' => $question_id,
        //         'kind'        => $source,
        //         'cate_id'     => $source_data['cate_id'] ?? 0,
        //         'paper_id'    => $source_data['paper_id'] ?? 0,
        //         'room_id'     => $source_data['room_id'] ?? 0,
        //     ],
        //     [
        //         'user_id'     => $user_id,
        //         'question_id' => $question_id,
        //         'user_answer' => $user_answer, //is_array($user_answer) ? json_encode($user_answer, JSON_UNESCAPED_UNICODE) : $user_answer,
        //         'kind'        => $source,
        //         'cate_id'     => $source_data['cate_id'] ?? 0,
        //         'paper_id'    => $source_data['paper_id'] ?? 0,
        //         'room_id'     => $source_data['room_id'] ?? 0,
        //     ]
        // );
    }
}
