<?php

namespace addons\exam\model;


use addons\exam\enum\CommonStatus;
use addons\exam\enum\UserScoreType;
use addons\exam\library\ScoreService;
use app\admin\model\exam\QuestionCollectModel;

class QuestionModel extends \app\admin\model\exam\QuestionModel
{
    // 字段类型
    protected $type
        = [
            'options_img' => 'array',
            // 'options_extend' => 'array',
            // 'options_json' => 'array'
        ];

    // 隐藏字段
    // protected $hidden = [
    //     'answer', 'explain'
    // ];

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

    public function getOptionsJsonAttr($value)
    {
        if ($value = json_decode($value, true)) {
            $data = [];
            foreach ($value as $key => $row) {
                $arr['key']         = $key;
                $arr['value']       = $row;
                $arr['click_index'] = false;
                array_push($data, $arr);
            }
            return $data;
        }
        return [];
    }

    public function getAnswerAttr($value, $data)
    {
        if (is_array($value)) {
            return $value;
        }
        if (in_array($data['kind'], ['FILL', 'SHORT'])) {
            return json_decode($value, true);
        }
        return $value;
    }

    // public function getOptionsExtendAttr($value)
    // {
    //     return json_decode($value, true);
    // }

    /**
     * 试题是否已收藏过
     *
     * @param $user_id
     * @param $questions
     * @return mixed
     */
    public static function isCollected($user_id, $questions)
    {
        $ids         = array_column($questions, 'id');
        $collects    = QuestionCollectModel::where('user_id', $user_id)->whereIn('question_id', $ids)->select();
        $collect_ids = array_column(collection($collects)->toArray(), 'question_id');

        foreach ($questions as &$question) {
            $question['collected'] = in_array($question['id'], $collect_ids);
        }

        return $questions;
    }

    /**
     * 获取试题列表
     *
     * @param $params
     * @return array|int
     */
    public static function getList($params, $decode = false)
    {
        $param = array_merge([
            'cate_id'        => 0,                            // 按分类查询
            'kind'           => '',                           // 试题类型查询
            'status'         => CommonStatus::NORMAL,         // 状态查询
            'keyword'        => '',                           // 搜索关键词
            'user_id'        => 0,                            // 传入用户ID时，查询收藏状态
            'page_count'     => 20,                           // 每页数量
            'sort'           => '',                           // 排序类型
            'just_get_count' => 0,                            // 仅获取题数
            'mode'           => 'normal',                     // normal=普通模式，memory=记忆模式，random=随机查询
            'memory_index'   => 0,                            // 记忆模式 - 上次做题题标
        ], $params);

        $model = new static();
        $model->with(['materialQuestions.question'])->where('is_material_child', 0);

        // 查询条件
        $where = [];
        if ($param['cate_id']) {
            // $model->where('cate_id', $param['cate_id']);
            $where['cate_id'] = $param['cate_id'];
        }
        if ($param['kind']) {
            // $model->where('kind', $param['kind']);
            $where['kind'] = $param['kind'];
        }
        if ($param['status']) {
            // $model->where('status', $param['status']);
            $where['status'] = $param['status'];
        }
        if ($param['keyword']) {
            // $model->where('title', 'like', '%' . $param['keyword'] . '%');
            $where['title'] = ['like', '%' . $param['keyword'] . '%'];
        }

        // 仅获取题数
        if ($param['just_get_count']) {
            return ['total' => $model->where($where)->count()];
        }

        // 能否获得积分标识
        $can_get_score = false;
        switch ($param['mode']) {
            // 记忆模式
            case 'memory':
                // 排序
                if ($param['sort']) {
                    $model->order($param['sort']);
                } else {
                    $model->order('id asc');
                }
                $list['data']  = $model->where($where)->select();
                $can_get_score = true;
                break;

            // 随机模式
            case 'random':
                // 限制最多500条
                $list['data']  = $model->where($where)->orderRaw('rand()')->limit(500)->select();
                $can_get_score = true;
                break;

            // 普通模式
            default:
                // 排序
                if ($param['sort']) {
                    $model->order($param['sort']);
                } else {
                    $model->order('id asc');
                }
                $list = $model->where($where)->paginate($param['page_count'])->toArray();
                // 练习/看题得积分，第一页时才得积分
                $can_get_score = input('page/d') == 1;
                break;
        }

        // 仅获取题数
        // if ($param['just_get_count']) {
        //     // if (!empty($params['user_id']) && $params['user_id'] == 5134) {
        //     //     dd($list['data'], count($list['data']));
        //     // }
        //
        //     if ($list['data']) {
        //         // 合并材料题子题目
        //         return ['total' => count(self::mergeMaterialQuestions($list['data']))];
        //         // $list['data'] = ;
        //     }
        //     return ['total' => $model->count()];
        // }

        if ($list['data']) {
            // 题目是否已收藏
            if ($param['user_id']) {
                $list['data'] = self::isCollected($param['user_id'], $list['data']);
            }

            if ($can_get_score) {
                $score_type    = input('type', '') == 'train' ? UserScoreType::TRAIN : UserScoreType::LOOK;
                $list['point'] = [
                    'get_point' => ScoreService::getScore($param['user_id'], $score_type),
                    'type'      => UserScoreType::getDescription($score_type),
                ];
            }

            // 合并材料题子题目
            $list['data'] = self::mergeMaterialQuestions($list['data']);

            // if ($decode) {
            //     foreach ($list['data'] as &$question) {
            //         // $question['title']   = ;
            //         $question['explain'] = htmlspecialchars_decode(html_entity_decode($question['explain']));
            //     }
            // }
        }

        return $list;
    }

    /**
     * 合并材料题子题目
     *
     * @param $questions
     * @return mixed
     */
    public static function mergeMaterialQuestions($questions)
    {
        $questions = collection($questions)->toArray();
        // dd($questions);
        $material_questions = [];
        foreach ($questions as $key => $question) {
            if ($question['kind'] == 'MATERIAL') {
                foreach ($question['material_questions'] as $material_question) {
                    // dd([$question]);
                    $new_question = $material_question['question'];

                    $new_question['material_id']    = $question['id'];
                    $new_question['material_title'] = $question['title'];
                    $new_question['material_score'] = $question['score'] ?? 1;
                    $new_question['origin_answer']  = $material_question['question']['answer'];
                    $new_question['score']          = $material_question['score'];
                    $new_question['answer']         = $material_question['answer'];

                    // with查询导致的数据格式问题，需要特殊处理
                    if (in_array($new_question['kind'], ['FILL', 'SHORT']) && $new_question['answer'] && is_string($new_question['answer'])) {
                        $new_question['answer'] = json_decode($new_question['answer'], true);
                    }
                    if ($new_question['options_img'] && is_string($new_question['options_img'])) {
                        $new_question['options_img'] = json_decode($new_question['options_img'], true);
                    }
                    if ($new_question['options_json'] && is_string($new_question['options_json'])) {
                        $new_question['options_json'] = json_decode($new_question['options_json'], true);

                        // 特殊格式处理
                        $keys = array_keys($new_question['options_json']);
                        if (isset($keys[0]) && $keys[0] && !is_numeric($keys[0])) {
                            $options_json = [];
                            foreach ($new_question['options_json'] as $option_key => $option_val) {
                                $options_json[] = [
                                    'key'         => $option_key,
                                    'value'       => $option_val,
                                    'click_index' => false,
                                ];
                            }
                            $new_question['options_json'] = $options_json;
                        }
                    }
                    $new_question['show_full'] = false;

                    $material_questions[] = $new_question;

                    // $material_question['question']['material_id']    = $question['id'];
                    // $material_question['question']['material_title'] = $question['title'];
                    // $material_question['question']['material_score'] = $question['score'] ?? 1;
                    // $material_question['question']['origin_answer']  = $material_question['question']['answer'];
                    // $material_question['question']['answer']         = $material_question['answer'];
                    // $material_question['question']['score']          = $material_question['score'];
                    // $material_question['question']['show_full']      = false;
                    // if ($material_question['question']['options_img'] && is_string($material_question['question']['options_img'])) {
                    //     $material_question['question']['options_img'] = json_decode($material_question['question']['options_img'], true);
                    // }
                    // if ($material_question['question']['options_json'] && is_string($material_question['question']['options_json'])) {
                    //     $material_question['question']['options_json'] = json_decode($material_question['question']['options_json'], true);
                    //
                    //     // 特殊格式处理
                    //     $keys = array_keys($material_question['question']['options_json']);
                    //     if (isset($keys[0]) && $keys[0] && !is_numeric($keys[0])) {
                    //         $options_json = [];
                    //         foreach ($material_question['question']['options_json'] as $option_key => $option_val) {
                    //             $options_json[] = [
                    //                 'key'         => $option_key,
                    //                 'value'       => $option_val,
                    //                 'click_index' => false,
                    //             ];
                    //         }
                    //         $material_question['question']['options_json'] = $options_json;
                    //     }
                    // }
                    //
                    // $material_questions[] = $material_question['question'];
                }

                // 删除材料题
                unset($questions[$key]);
                // array_splice($questions, $key, 1);
                // dd($questions);
                // array_slice($questions, $key, 1);
            }
            // dd(collection($material_questions)->toArray());
        }
        // dd(collection($questions)->toArray());
        // dd(collection($material_questions)->toArray());

        if ($material_questions) {
            $questions = array_merge(array_values($questions), $material_questions);
        }

        return $questions;
    }

    /**
     * 设置材料子题题的材料题干
     *
     * @param $questions
     * @return array
     */
    public static function setQuestionsMaterialParent($questions)
    {
        $questions = collection($questions)->toArray();
        foreach ($questions as &$question) {
            if (!empty($question['material_parent'])) {
                $question['material_title'] = $question['material_parent']['title'];
            }
        }
        return $questions;
    }
}
