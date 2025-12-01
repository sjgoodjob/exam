<?php

namespace app\admin\controller\exam;

use addons\exam\enum\CommonStatus;
use addons\exam\library\FrontService;
use app\admin\model\exam\MaterialQuestionModel;
use app\admin\model\exam\QuestionModel;
use app\common\controller\Backend;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Session;

/**
 * 试题
 *
 * @icon fa fa-circle-o
 */
class Question extends Backend
{

    /**
     * QuestionModel模型对象
     *
     * @var \app\admin\model\exam\QuestionModel
     */
    protected $model = null;
    protected $noNeedRight = ['*'];
    protected $multiFields = 'status';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\exam\QuestionModel;
        $this->view->assign("kindList", $this->model->getKindList());
        $this->view->assign("difficultyList", $this->model->getDifficultyList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }



    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();

            $list = $this->model
                ->with(['cate'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                if (isset($row['cate'])) {
                    $row->getRelation('cate')->visible(['name']);
                }
            }

            $count = $list->total();
            $items = $list->items();

            // 检测是否有重复题目
            foreach ($items as &$question) {
                $title = str_replace(' ', '', $question['title']);;
                $question['is_repeat'] = 0;

                foreach ($items as $item) {
                    if ($item['id'] != $question['id']) {
                        $title2 = str_replace(' ', '', $item['title']);
                        if ($title == $title2) {
                            // 标记重复
                            $question['is_repeat'] = 1;
                            break;
                        }
                    }
                }
            }

            $result = ["total" => $count, "rows" => $items];

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            // if ($params['options_extend']) {
            //     $params['options_extend'] = json_decode(urldecode($params['options_extend']), true);
            // }
            // dd($params);
            if ($params) {
                $params = $this->preExcludeFields($params);

                // 检查题目输入
                $this->checkTitle($params);
                // 检查答案输入
                $this->checkAnswer($params);
                // 处理选项图片链接域名
                $this->optionsImage($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);

                    // 保存材料题父题目
                    $this->saveMaterialParentQuestion($this->model);
                    // 保存材料题子题目
                    $this->saveMaterialQuestions($this->model, $params['material_questions'] ?? []);

                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids, [
            'material_questions' => function ($query) {
                return $query->with([
                    'question' => function ($query) {
                        return $query->with('cates');
                    },
                ])->order('weigh');
            },
        ]);

        // dd($row->toArray());
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                // 检查题目输入
                $this->checkTitle($params);
                // 检查答案输入
                $this->checkAnswer($params);
                // 处理选项图片链接域名
                $this->optionsImage($params);
                // 检查处理材料题相关
                $this->checkMaterial($params, $row);

                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name     = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);

                    // 保存材料题父题目
                    $this->saveMaterialParentQuestion($row);
                    // 保存材料题子题目
                    $this->saveMaterialQuestions($row, $params['material_questions'] ?? []);

                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 选项图片页面
     *
     * @return string
     */
    public function image()
    {
        if ($this->request->isPost()) {
            $this->success($this->request->post());
        }
        return $this->view->fetch();
    }

    /**
     * 试题导入
     */
    public function importExcel()
    {
        if ($this->request->isPost()) {
            // parent::import();
            $cate = $this->request->param('cate');
            // $exam_type = $this->request->param('exam_type');
            if (!$cate) { //  || !$exam_type
                $this->error('请先选择所属类型及考试分类再进行上传');
            }

            $file = $this->request->request('file');
            if (!$file) {
                $this->error(__('Parameter %s can not be empty', 'file'));
            }

            $filePath = ROOT_PATH . DS . 'public' . DS . $file;
            if (!is_file($filePath)) {
                $this->error(__('No results were found'));
            }

            //实例化reader
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
                $this->error(__('Unknown data format'));
            }

            if ($ext === 'csv') {
                $file     = fopen($filePath, 'r');
                $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
                $fp       = fopen($filePath, "w");
                $n        = 0;
                while ($line = fgets($file)) {
                    $line     = rtrim($line, "\n\r\0");
                    $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
                    if ($encoding != 'utf-8') {
                        $line = mb_convert_encoding($line, 'utf-8', $encoding);
                    }
                    if ($n == 0 || preg_match('/^".*"$/', $line)) {
                        fwrite($fp, $line . "\n");
                    } else {
                        fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
                    }
                    $n++;
                }
                fclose($file) || fclose($fp);

                $reader = new Csv();
            } elseif ($ext === 'xls') {
                $reader = new Xls();
            } else {
                $reader = new Xlsx();
            }

            //加载文件
            $insert = [];
            try {
                if (!$PHPExcel = $reader->load($filePath)) {
                    throw new \Exception(__('Unknown data format'));
                }
                $currentSheet    = $PHPExcel->getSheet(0);                //读取文件中的第一个工作表
                $allColumn       = $currentSheet->getHighestDataColumn(); //取得最大的列号
                $allRow          = $currentSheet->getHighestRow();        //取得一共有多少行
                $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);
                $fields          = [
                    'kind',
                    'title',
                    'explain',
                    'difficulty',
                    'answer',
                    'A',
                    'B',
                    'C',
                    'D',
                    'E',
                    'F',
                    'G',
                    'H',
                ];

                $time = time();
                for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                    $values = [];
                    for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                        $val      = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                        $values[] = is_null($val) ? '' : $val;

                    }
                    // $temp    = array_combine($fields, $values);
                    $row     = [];
                    $options = [];

                    // 选项及字段组合
                    for ($i = 0; $i < min($maxColumnNumber, count($fields)); $i++) {
                        $field = $fields[$i];
                        $value = $values[$i];

                        if ($i > 4) {
                            if (!empty($value) || $value == '0') {
                                $options[$field] = $value;
                            }
                        } else {
                            $row[$field] = $value;
                        }
                    }

                    // 过滤空行
                    if (!$row['title']) {
                        continue;
                    }

                    // 特殊字段处理
                    foreach ($row as $key => $item) {
                        if ($key == 'kind') {
                            switch ($item) {
                                case '判断':
                                case '判断题':
                                    $item = 'JUDGE';
                                    break;
                                case '单选':
                                case '单选题':
                                    $item = 'SINGLE';
                                    break;
                                case '多选':
                                case '多选题':
                                    $item = 'MULTI';
                                    break;
                                case '填空':
                                case '填空题':
                                    $item = 'FILL';
                                    break;
                                case '简答':
                                case '简答题':
                                    $item = 'SHORT';
                                    break;
                            }
                        } else if ($key == 'difficulty') {
                            switch ($item) {
                                case '低':
                                case '简单':
                                    $item = 'EASY';
                                    break;
                                case '高':
                                case '困难':
                                    $item = 'HARD';
                                    break;
                                default:
                                    $item = 'GENERAL';
                                    break;
                            }
                        }

                        $row[$key] = $item;
                    }

                    // 判断题特殊情况处理
                    if ($row['kind'] == 'JUDGE') {
                        switch ($row['answer']) {
                            case '正确':
                            case '对':
                                $row['answer'] = 'A';
                                break;
                            case '错误':
                            case '错':
                                $row['answer'] = 'B';
                                break;
                        }

                        $options = $options ? $options : ['A' => '正确', 'B' => '错误'];
                    }

                    // 答案特殊处理
                    $row['answer'] = str_replace(' ', '', $row['answer']);
                    $row['answer'] = str_replace(' ', '', $row['answer']);
                    $row['answer'] = str_replace('，', ',', $row['answer']);

                    if ($row['answer'] == '') {
                        throw new \Exception('题目【' . $row['title'] . '】答案格式有误');
                    }
                    if ($row['kind'] == 'MULTI') {
                        if (!strpos($row['answer'], ',')) {
                            $multi_answer = str_split($row['answer']);
                            if (!$multi_answer) {
                                throw new \Exception('题目【' . $row['title'] . '】答案格式有误');
                            }

                            $row['answer'] = $multi_answer;
                        }
                    }

                    // 填空题
                    if ($row['kind'] == 'FILL') {
                        $row['answer'] = explode('|||', $row['answer']);
                        $fill_count    = count(explode('______', $row['title'])) - 1;
                        $answer_count  = count($row['answer']);

                        if ($fill_count != $answer_count) {
                            throw new \Exception('题目【' . $row['title'] . '】填空位与答案数量不匹配');
                        }

                        $fill_answers = [];
                        foreach ($row['answer'] as $item) {
                            $fill_answers[] = ['answers' => explode(',', $item)];
                        }

                        $row['answer'] = json_encode($fill_answers, JSON_UNESCAPED_UNICODE);
                    }
                    // 简答题
                    if ($row['kind'] == 'SHORT') {
                        $row['answer'] = explode('|||', $row['answer']);
                        if ($row['answer']) {
                            $short_answers = [
                                'answer' => $row['answer'][0],
                                'config' => [],
                            ];

                            if (isset($row['answer'][1]) && $row['answer'][1]) {
                                $keywords = explode(',', str_replace('，', ',', $row['answer'][1]));
                                foreach ($keywords as $keyword) {
                                    $short_answers['config'][] = [
                                        'answer' => $keyword,
                                        'score'  => 1,
                                    ];
                                }
                            }

                            $row['answer'] = json_encode($short_answers, JSON_UNESCAPED_UNICODE);
                        }
                    }

                    $row['options_json'] = json_encode($options, JSON_UNESCAPED_UNICODE);
                    $row['cate_id']      = $cate;
                    $row['createtime']   = $time;
                    // $row['exam_type_id'] = $exam_type;
                    $insert[] = $row;
                }

                Session::set('import_question', $insert);
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }

            if (!$insert) {
                $this->error(__('No rows were updated'));
            }
            $this->success('识别成功', '', ['count' => count($insert)]);
        }
        $this->error('错误的提交方式');
    }

    /**
     * 试题导入提交
     *
     * @return string|void
     */
    public function import()
    {
        if ($this->request->isPost()) {
            // 加载数据
            $insert = Session::pull('import_question');
            if (!$insert) {
                $this->error(__('没有可以导入的数据，请重新上传Excel文件'));
            }

            try {
                $this->model->saveAll($insert);
            } catch (PDOException $exception) {
                $msg = $exception->getMessage();
                if (preg_match("/.+Integrity constraint violation: 1062 Duplicate entry '(.+)' for key '(.+)'/is", $msg, $matches)) {
                    $msg = "导入失败，包含【{$matches[1]}】的记录已存在";
                };
                $this->error($msg);
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }

            $this->success();
        }

        return $this->view->fetch();
    }

    /**
     * 获取题库数量
     */
    public function getCount()
    {
        $cate_ids = $this->request->param('cate_ids');

        if (!$cate_ids) {
            $this->error('请先选择题库');
        }

        $this->success('', '', $this->model->getCount($cate_ids));
    }

    /**
     * 选择题目页面
     */
    public function select()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();

            $list = $this->model
                ->with(['cate'])
                ->where($where)
                ->where('question_model.status', CommonStatus::NORMAL)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->getRelation('cate')->visible(['name']);
            }
            $rows = $list->items();
            foreach ($rows as &$row) {
                $row['title'] = strip_tags($row['title']);
            }

            $result = ["total" => $list->total(), "rows" => $rows];

            return json($result);
        }
        return $this->view->fetch();
    }

    public function test()
    {
        $this->success('', '', Session::get('import_question'));
    }

    /**
     * 检查答案输入
     *
     * @param $params
     */
    protected function checkAnswer(&$params)
    {
        $answer  = $params['answer'] ?? '';
        $options = $params['options_json'] ?? [];

        if (in_array($params['kind'], ['MULTI', 'JUDGE', 'SINGLE'])) {
            if (!$answer) {
                $this->error('请设置题目答案');
            }
            if (!$options) {
                $this->error('请设置题目选项');
            }

            $options     = json_decode($options, true);
            $option_keys = array_keys($options);

            // 多选题
            if (strpos($answer, ',') !== false) {
                $answer_arr = explode(',', trim($answer));
                foreach ($answer_arr as $item) {
                    if (!in_array($item, $option_keys)) {
                        $this->error('答案设置有误，答案选项不存在');
                    }
                }
            } else {
                if (!in_array($answer, $option_keys)) {
                    $this->error('答案设置有误，答案选项不存在');
                }
            }
        }

        // 后期拓展题型
        switch ($params['kind']) {
            // 填空题
            case 'FILL':
                if (!is_array($answer) || !count($answer)) {
                    $this->error('未设置填空题答案，至少设置一个');
                }

                // 转json
                $params['answer'] = json_encode($params['answer'], JSON_UNESCAPED_UNICODE);
                break;

            // 简答题
            case 'SHORT':
                $answer = $params['short_answer'] ?? '';
                if (!$answer) {
                    $this->error('请设置简答题标准答案');
                }
                $answer_config = $params['answer_config'] ?? [];
                if (!$answer_config) {
                    $this->error('请设置简答题答案关键词');
                }

                foreach ($answer_config as &$item) {
                    if (!$item['answer']) {
                        $this->error('简答题答案关键词不能为空');
                    }
                    if (!is_numeric($item['score']) || $item['score'] < 0) {
                        $this->error('简答题分数设置有误，必须为数字且不能小于0');
                    }

                    // $item['score'] = 0;
                }

                // 转json
                $params['answer'] = json_encode([
                    'answer' => $answer,
                    'config' => $answer_config,
                ], JSON_UNESCAPED_UNICODE);
                break;

            // 材料题
            case 'MATERIAL':
                // dd([$params, $answer, $options]);

                if ($params['is_material_child']) {
                    $this->error('题型设置有误，材料题不能再设置为材料题的子题');
                }

                $params['is_material_child']    = 0;
                $params['material_question_id'] = 0;
                $params['material_questions']   = $params['material_questions'] ? json_decode($params['material_questions'], true) : [];
                if (!$params['material_questions']) {
                    if ($params['status'] == CommonStatus::NORMAL) {
                        $this->error('请设置材料题的子题（或者状态先设置隐藏）');
                    }
                }

                // foreach ($params['material_questions'] as $material_question) {
                //     if ($material_question['kind'] == 'MATERIAL') {
                //         $this->error('材料题的子题不能为材料题');
                //     }
                // }

                // 转json
                $params['answer'] = json_encode([
                    'questions' => $params['material_questions'],
                ], JSON_UNESCAPED_UNICODE);
                break;
        }

        if ($params['is_material_child']) {
            $params['material_question_id'] = $params['material_question_id'] ?? 0;
            if (!$params['material_question_id']) {
                $this->error('请设置材料题的父题');
            }
            if ($params['material_score'] <= 0) {
                $this->error('请设置材料题子题的分数');
            }
        }
    }

    /**
     * 保存材料题父题
     *
     * @param $question
     * @return void
     */
    protected function saveMaterialParentQuestion($question)
    {
        if (!$question || !$question['is_material_child'] || $question['kind'] == 'MATERIAL') {
            return;
        }
        if ($parentQuestion = QuestionModel::where('id', $question['material_question_id'])
            ->where('kind', 'MATERIAL')
            ->find()) {
            $answer = json_decode($parentQuestion->answer, true);
            if (!$answer) {
                $answer = [
                    'questions' => [
                        [
                            'id'            => $question['id'],
                            'score'         => $question['material_score'],
                            'answer'        => is_array($question['answer']) ? json_encode($question['answer'], JSON_UNESCAPED_UNICODE) : $question['answer'],
                            'answer_config' => null,
                        ],
                    ],
                ];
            } else {
                $has_child = false;
                foreach ($answer['questions'] as &$child_question) {
                    if ($child_question['id'] == $question['id']) {
                        $has_child                = true;
                        $child_question['score']  = $question['material_score'];
                        $child_question['answer'] = is_array($question['answer']) ? json_encode($question['answer'], JSON_UNESCAPED_UNICODE) : $question['answer'];
                        break;
                    }
                }

                if (!$has_child) {
                    $answer['questions'][] = [
                        'id'            => $question['id'],
                        'score'         => $question['material_score'],
                        'answer'        => is_array($question['answer']) ? json_encode($question['answer'], JSON_UNESCAPED_UNICODE) : $question['answer'],
                        'answer_config' => null,
                    ];
                }
            }

            // 材料题父题答案追加子题设置
            $parentQuestion->answer = json_encode($answer, JSON_UNESCAPED_UNICODE);
            $parentQuestion->save();

            if ($materialQuestion = MaterialQuestionModel::where('parent_question_id', $question['material_question_id'])
                ->where('question_id', $question['id'])
                ->find()) {
                $materialQuestion->score  = $question['material_score'];
                $materialQuestion->answer = is_array($question['answer']) ? json_encode($question['answer'], JSON_UNESCAPED_UNICODE) : $question['answer'];
                $materialQuestion->save();
            } else {
                // 保存材料题子题关联
                MaterialQuestionModel::create([
                    'parent_question_id' => $question['material_question_id'],
                    'question_id'        => $question['id'],
                    'score'              => $question['material_score'],
                    'answer'             => $question['answer'],
                    'weigh'              => 0,
                ]);
            }

        } else {
            $this->error('未找到所属材料题');
        }
    }

    /**
     * 保存材料题子题
     *
     * @param $parentQuestion
     * @param $questions
     * @return void
     */
    protected function saveMaterialQuestions($parentQuestion, $questions)
    {
        if (!$parentQuestion || $parentQuestion['kind'] != 'MATERIAL' || !$questions) {
            return;
        }
        // 删除旧的子题
        MaterialQuestionModel::where('parent_question_id', $parentQuestion->id)->delete();

        // 保存新的子题
        $inserts      = [];
        $question_ids = [];
        foreach ($questions as $key => $question) {
            $inserts[] = [
                'parent_question_id' => $parentQuestion->id,
                'question_id'        => $question['id'],
                'score'              => $question['score'],
                'answer'             => is_array($question['answer']) ? json_encode($question['answer'], JSON_UNESCAPED_UNICODE) : $question['answer'],
                'weigh'              => $key,
                'createtime'         => time(),
            ];

            $question_ids[] = $question['id'];
        }
        (new MaterialQuestionModel())->saveAll($inserts);

        if ($question_ids) {
            // 将材料题子题目改为【属于材料题子题】
            QuestionModel::where('id', 'in', $question_ids)->update([
                'is_material_child'    => 1,
                'material_question_id' => $parentQuestion->id,
            ]);
        }
    }

    /**
     * 处理选项图片链接域名
     *
     * @param $data
     */
    protected function optionsImage(&$data)
    {
        $options_img = $data['options_img'] ?? [];
        $options_img = json_decode($options_img, true);
        if ($options_img) {
            foreach ($options_img as &$item) {
                if (strpos($item['value'], '://') === false) {
                    $item['value'] = cdnurl($item['value'], true);
                }
            }
        }
        $data['options_img'] = json_encode($options_img);
        $data['options_img'] = $data['options_img'] == 'null' ? [] : $data['options_img'];
    }

    /**
     * 检查题目内容输入
     *
     * @param $params
     */
    protected function checkTitle(&$params)
    {
        if (in_array($params['kind'], ['MULTI', 'JUDGE', 'SINGLE'])) {
            if (!($params['title'] ?? '')) {
                $this->error('请输入题目内容');
            }
        } else if ($params['kind'] == 'FILL') {
            if (!($params['title_fill'] ?? '')) {
                $this->error('请输入填空题题目内容');
            }
            $params['title'] = $params['title_fill'];
        }

        // 替换题目图片CDN链接
        $params['title'] = FrontService::replaceImgUrl($params['title']);
    }

    /**
     * 检查材料题
     *
     * @param array $params
     * @param array $row
     */
    protected function checkMaterial($params, $row = null)
    {
        // if ($params['kind'] != 'MATERIAL') {
        //     return;
        // }

        if ($params['kind'] == 'MATERIAL') {
            if (!empty($params['is_material_child'])) {
                $this->error('当前题目是材料题主题目，不能设置为材料题子题');
            }
        }

        if (!empty($params['material_question_id'])) {
            if ($row) {
                if ($params['material_question_id'] != $row['id']) {
                    $this->error('材料题题目不能与所属材料题题目相同');
                }
            }
        }


    }
    public function ai()
    {
        return $this->view->fetch(); // 或 return view();
    }

    public function doAIGenerate()
    {
        $params = $this->request->post();
    
        // 参数解析
        $prompt = $params['prompt'] ?? '';
        $single = intval($params['single'] ?? 0);
        $multi = intval($params['multi'] ?? 0);
        $judge = intval($params['judge'] ?? 0);
        $fill  = intval($params['fill'] ?? 0);
        $short = intval($params['short'] ?? 0);
    
        $total = $single + $multi + $judge + $fill + $short;
        if (!$prompt || $total === 0) {
            $this->error("请输入命题内容并设置题目数量");
        }
    
        // 拼接题型描述
        $typeText = [];
        if ($single) $typeText[] = "单选{$single}题";
        if ($multi)  $typeText[] = "多选{$multi}题";
        if ($judge)  $typeText[] = "判断{$judge}题";
        if ($fill)   $typeText[] = "填空{$fill}题";
        if ($short)  $typeText[] = "问答{$short}题";
        $typeStr = implode('，', $typeText);
    
        // 构造 Prompt
        $aiPrompt = <<<PROMPT
    请根据以下教学内容生成 {$total} 道题，题型包括：{$typeStr}。
    
    每题必须包含字段：
    - kind：题型（SINGLE, MULTI, JUDGE, FILL, SHORT）
    - title：题目内容,请不要包含中文解析
    - options：选项数组（仅 SINGLE 和 MULTI 必填）,如果是选择题,不需要带A,B,C,D之类的,如果是简答题，请请返回答案关键词
    - answer：正确答案选项
    - explanation：答案解析说明,请用中文
    
    必须返回**严格格式的 JSON 数组**，不要包含任何说明文字、Markdown、标题或自然语言解释。
    
    教学内容如下：
    【{$prompt}】
    PROMPT;
    
        // 调用豆包 Ark API
        $apiKey = '8b1a028a-baf3-4535-b4c1-2ee222c40634'; // ← 请替换为你实际的 Ark API Key
        $apiUrl = 'https://ark.cn-beijing.volces.com/api/v3/chat/completions';
    
        $messages = [
            ["role" => "system", "content" => "你是一个专业命题AI助手，负责生成结构化题目数据。"],
            ["role" => "user", "content" => $aiPrompt]
        ];
    
        $postData = [
            "model" => "doubao-1-5-thinking-pro-250415",
            "messages" => $messages,
            "temperature" => 0.7,
            "response_format" => [
                "type" => "json_schema",
                "json_schema" => [
                    "name" => "exam_questions",
                    "schema" => [
                        "type" => "array",
                        "items" => [
                            "type" => "object",
                            "properties" => [
                                "kind" => [ "type" => "string" ],
                                "title" => [ "type" => "string" ],
                                "options" => [
                                    "type" => "array",
                                    "items" => [ "type" => "string" ]
                                ],
                                "answer" => [ "type" => "string" ],
                                "explanation" => [ "type" => "string" ]
                            ],
                            "required" => ["kind", "title", "answer", "explanation"],
                            "additionalProperties" => false
                        ]
                    ],
                    "strict" => true
                ]
            ],
            "thinking" => [
                "type" => "disabled"
            ]
            
        ];
    
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$apiKey}"
        ];
    
        // 发起 CURL 请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        $result = curl_exec($ch);
        $error  = curl_error($ch);
        curl_close($ch);
        file_put_contents(RUNTIME_PATH . 'ai_debug.log', $result);  // ① 写入日志
        
        if ($error) {
            $this->error("请求失败：" . $error);
        }
    
        $res = json_decode($result, true);
        $text = $res['choices'][0]['message']['content'] ?? '';
    
        // 尝试解析返回 JSON
        $data = json_decode($text, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->error("AI返回内容不是有效JSON，请检查提示词或格式。\n原文：" . $text);
        }
    
        // 校验字段完整性
        foreach ($data as $index => $item) {
            if (!isset($item['kind'], $item['title'], $item['answer'], $item['explanation'])) {
                $this->error("第 " . ($index + 1) . " 题缺少字段");
            }
        }
    
        // 成功返回题目数据
        $this->success("生成成功",null, ['questions' => $data]);
    }
    
}
