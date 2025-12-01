<?php

namespace addons\exam\controller;

use addons\exam\model\SubjectModel;
use addons\exam\model\UserInfoModel;

/**
 * 科目接口
 */
class Subject extends Base
{
    protected $noNeedLogin = ['index', 'detail'];
    protected $noNeedRight = ['*'];

    /**
     * 科目列表
     */
    public function index()
    {
        $only_subject_ids = '';
        if ($this->auth && $this->auth->id) {
            $user_info = UserInfoModel::getUserInfo($this->auth->id);
            if ($user_info['only_subject_ids']) {
                $only_subject_ids = explode(',', $user_info['only_subject_ids']);
            }
        }

        $query = SubjectModel::with([
            'child' => function ($query) use ($only_subject_ids) {
                $query->field('id,name,parent_id,weigh');
                if ($only_subject_ids) {
                    $query->where('id', 'in', $only_subject_ids);
                }
            },
        ])
            ->where('status', '1')
            ->where('level', 1);

        $list = $query->order('weigh desc')->select();
        $list = $list ? collection($list)->toArray() : [];

        $data = [];
        foreach ($list as $item) {
            if (empty($item['child'])) {
                continue;
            }

            $data[] = [
                'id'       => $item['id'],
                'name'     => $item['name'],
                'children' => $item['child'],
            ];
        }

        $this->success('', $data);
    }

    /**
     * 已绑定的科目列表
     */
    public function onlySubject()
    {
        $only_subject_ids = '';
        $user_info        = UserInfoModel::getUserInfo($this->auth->id);
        if ($user_info['only_subject_ids']) {
            $only_subject_ids = explode(',', $user_info['only_subject_ids']);
        }

        if ($only_subject_ids) {
            $query = SubjectModel::with([
                'child' => function ($query) use ($only_subject_ids) {
                    $query->field('id,name,parent_id,weigh');
                    if ($only_subject_ids) {
                        $query->where('id', 'in', $only_subject_ids);
                    }
                },
            ])
                ->where('status', '1')
                ->where('level', 1);

            $list = $query->order('weigh desc')->select();
            $list = $list ? collection($list)->toArray() : [];

            $data = [];
            foreach ($list as $item) {
                if (empty($item['child'])) {
                    continue;
                }

                // 按weigh排序，按照weigh降序
                usort($item['child'], function ($a, $b) {
                    return $b['weigh'] <=> $a['weigh'];
                });

                $data[] = [
                    'id'       => $item['id'],
                    'name'     => $item['name'],
                    'children' => $item['child'],
                ];
            }
        } else {
            $data = [];
        }

        $this->success('', $data);
    }

    /**
     * 获取科目内容
     */
    public function detail()
    {
        $subject_id = input('subject_id/d');
        if (!$subject_id) {
            $this->error('参数错误');
        }

        $subject = SubjectModel::with([
            'parent' => function ($query) {
                $query->field('id,name');
            },
        ])
            ->where('id', $subject_id)
            ->field('id,name,parent_id')
            ->find();
        if (!$subject) {
            $this->error('科目不存在');
        }

        $this->success('', $subject);
    }
}
