<?php

namespace app\admin\model\exam;

use addons\exam\enum\CateUses;
use addons\exam\model\BaseModel;
use traits\model\SoftDelete;

class MemberConfigModel extends BaseModel
{

    use SoftDelete;


    // 表名
    protected $name = 'exam_member_config';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text',
        'uses_text',
        'paper_uses_text',
    ];


    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getUsesList()
    {
        return ['all' => __('Uses all'), 'cate' => __('Uses cate')];
    }

    public function getUsesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['uses']) ? $data['uses'] : '');
        $list  = $this->getUsesList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getPaperUsesList()
    {
        return ['all' => __('paper_uses all'), 'part_cate' => __('paper_uses part_cate')];
    }

    public function getPaperUsesTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['paper_uses']) ? $data['paper_uses'] : '');
        $list  = $this->getUsesList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    /**
     * 设置可用题库为仅会员可用
     *
     * @param $member_config
     * @return void
     */
    public static function setCateOnlyMemberUses($member_config)
    {
        if ($member_config['uses'] == 'cate' && $member_config['cate_ids']) {
            $cate_ids = explode(',', $member_config['cate_ids']);
            \app\admin\model\exam\CateModel::where('id', 'in', $cate_ids)->update([
                'uses' => CateUses::ONLY_MEMBER,
            ]);

            foreach ($cate_ids as $cate_id) {
                $child_cate_ids_2 = CateModel::getChildId($cate_id);
                if ($child_cate_ids_2) {
                    \app\admin\model\exam\CateModel::where('id', 'in', $child_cate_ids_2)->update([
                        'uses' => CateUses::ONLY_MEMBER,
                    ]);

                    foreach ($child_cate_ids_2 as $cate_id_2) {
                        $child_cate_ids_3 = CateModel::getChildId($cate_id_2);
                        if ($child_cate_ids_3) {
                            \app\admin\model\exam\CateModel::where('id', 'in', $child_cate_ids_3)->update([
                                'uses' => CateUses::ONLY_MEMBER,
                            ]);
                        }
                    }
                }
            }
        }

        if (isset($member_config['paper_uses']) && $member_config['paper_uses'] == 'part_cate' && $member_config['paper_cate_ids']) {
            $paper_cate_ids = explode(',', $member_config['paper_cate_ids']);
            // dd($paper_cate_ids);
            \app\admin\model\exam\CateModel::where('id', 'in', $paper_cate_ids)->update([
                'uses' => CateUses::ONLY_MEMBER,
            ]);
            // \app\admin\model\exam\PaperModel::where('id', 'in', $paper_cate_ids)->update([
            //     'uses' => CateUses::ONLY_MEMBER,
            // ]);

            foreach ($paper_cate_ids as $paper_cate_id) {
                $child_paper_cate_ids_2 = CateModel::getChildId($paper_cate_id);
                if ($child_paper_cate_ids_2) {
                    \app\admin\model\exam\CateModel::where('id', 'in', $child_paper_cate_ids_2)->update([
                        'uses' => CateUses::ONLY_MEMBER,
                    ]);
                    // \app\admin\model\exam\PaperModel::where('id', 'in', $child_paper_cate_ids_2)->update([
                    //     'uses' => CateUses::ONLY_MEMBER,
                    // ]);

                    foreach ($child_paper_cate_ids_2 as $paper_cate_id_2) {
                        $child_paper_cate_ids_3 = CateModel::getChildId($paper_cate_id_2);
                        if ($child_paper_cate_ids_3) {
                            \app\admin\model\exam\CateModel::where('id', 'in', $child_paper_cate_ids_3)->update([
                                'uses' => CateUses::ONLY_MEMBER,
                            ]);
                            // \app\admin\model\exam\PaperModel::where('id', 'in', $child_paper_cate_ids_3)->update([
                            //     'uses' => CateUses::ONLY_MEMBER,
                            // ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * 获取可用题库名称
     *
     * @param $member_config
     * @return array
     */
    public static function getCateNames($member_config)
    {
        if ($member_config['uses'] != 'cate') {
            return [];
        }

        $cate_ids = explode(',', $member_config['cate_ids']);
        if (!$cate_ids) {
            return [];
        }

        $cate_names = [];
        $cates      = \app\admin\model\exam\CateModel::where('id', 'in', $cate_ids)
            ->where('status', 1)
            ->order('level asc, sort desc')
            ->select();
        // if ($member_config['id'] == 1) {
        //     ddd(collection($cates)->toArray());
        // }
        foreach ($cates as $cate) {
            if (intval($cate['level']) != 3) {
                $cate_names[] = $cate['name'] . '及所属子类';
            } else {
                $cate_names[] = $cate['name'];
            }
        }

        return $cate_names;
    }

    /**
     * 获取可用试卷分类名称
     *
     * @param $member_config
     * @return array
     */
    public static function getPaperCateNames($member_config)
    {
        if ($member_config['paper_uses'] != 'part_cate') {
            return [];
        }

        $cate_ids = explode(',', $member_config['paper_cate_ids']);
        if (!$cate_ids) {
            return [];
        }

        $cate_names = [];
        $cates      = \app\admin\model\exam\CateModel::where('id', 'in', $cate_ids)
            ->where('status', 1)
            ->order('level asc, sort desc')
            ->select();
        foreach ($cates as $cate) {
            if (intval($cate['level']) != 3) {
                $cate_names[] = $cate['name'] . '及所属子类';
            } else {
                $cate_names[] = $cate['name'];
            }
        }

        return $cate_names;
    }
}
