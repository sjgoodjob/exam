<?php

namespace app\admin\model\exam;

use addons\exam\enum\CateKind;
use addons\exam\enum\CateUses;
use addons\exam\model\BaseModel;
use addons\exam\model\CateUserLogModel;
use addons\exam\model\UserInfoModel;
use think\Config;
use think\Db;
use traits\model\SoftDelete;
use addons\exam\model\UserModel;


class CateModel extends BaseModel
{
    use SoftDelete;

    // 表名
    protected $name = 'exam_cate';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append
        = [
            'kind_text',
            'level_text',
        ];

    protected static function init()
    {
        // self::beforeWrite(function ($row) {
        //     if (!$row['parent_id']) {
        //         $row['level'] = 1;
        //     } else {
        //         $parent = self::get($row['parent_id']);
        //
        //         if ($parent['level'] == 1) {
        //             $row['level'] = 2;
        //         } else {
        //             $row['level'] = 3;
        //         }
        //     }
        // }, true);

        self::afterWrite(function ($row) {
            if (!empty($row['subject_id'])) {
                // 更新试卷的科目
                if ($row['kind'] == CateKind::PAPER) {
                    PaperModel::where('cate_id', $row['id'])->update(['subject_id' => $row['subject_id']]);
                }

                // 更新考场的科目
                if ($row['kind'] == CateKind::ROOM) {
                    RoomModel::where('cate_id', $row['id'])->update(['subject_id' => $row['subject_id']]);
                }
            }
        });
    }

    public function subject()
    {
        return $this->belongsTo(SubjectModel::class, 'subject_id');
    }

    public function getKindList()
    {
        return [
            'QUESTION' => __('Question'),
            'ROOM'     => __('Room'),
            'PAPER'    => __('Paper'),
            // 'COURSE'   => __('Course'),
        ];
    }

    public function getLevelList()
    {
        return ['1' => __('Level 1'), '2' => __('Level 2'), '3' => __('Level 3')];
    }

    public function getUsesList()
    {
        return ['ALL' => __('ALL'), 'ONLY_MEMBER' => __('ONLY_MEMBER')];
    }

    public function getIsFreeList()
    {
        return ['0' => __('is_free 0'), '1' => __('is_free 1')];
    }

    public function getStatusList()
    {
        return ['0' => __('status 0'), '1' => __('status 1')];
    }

    public function getIsLookList()
    {
        return ['0' => __('is_look 0'), '1' => __('is_look 1')];
    }

    public function getKindTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['kind'] ?? '');
        $list  = $this->getKindList();
        return $list[$value] ?? '';
    }


    public function getLevelTextAttr($value, $data)
    {
        $value = $value ? $value : ($data['level'] ?? '');
        $list  = $this->getLevelList();
        return $list[$value] ?? '';
    }

    /**
     * 组装筛选三级分类格式
     *
     * @param string $kind
     * @param int    $subject_id
     * @return array[]
     */
    public static function threeLevel(string $kind, $subject_id = 0)
    {
        $data = self::all(function ($query) use ($kind, $subject_id) {
            if ($subject_id) {
                $query = $query->where('subject_id', $subject_id);
            }
            $query->where('status', '1')->where('kind', $kind)->order('sort desc');
        });

        $result = [
            [
                'name'    => '全部',
                'value'   => 'all',
                'submenu' => [
                    'name'  => '全部',
                    'value' => 'all',
                ],
            ],
        ];
        $level1 = [];
        $level2 = [];
        $level3 = [];

        // 分级
        foreach ($data as $item) {
            switch ($item['level']) {
                case 1:
                    array_push($level1, $item);
                    break;
                case 2:
                    array_push($level2, $item);
                    break;
                case 3:
                    array_push($level3, $item);
                    break;
            }
        }

        // 先组装level2和level3的数据，即level1的子级
        $level1_sub_menu = [];
        foreach ($level2 as $item2) {
            $sub_menu = [];
            foreach ($level3 as $item3) {
                if ($item3['parent_id'] == $item2['id']) {
                    $sub_menu[] = [
                        'name'  => $item3['name'],
                        'value' => $item3['id'],
                    ];
                }
            }

            $level1_sub_menu[] = [
                'name'      => $item2['name'],
                'value'     => $item2['id'],
                'submenu'   => $sub_menu,
                'parent_id' => $item2['parent_id'],
            ];
        }

        // 装载level1
        foreach ($level1 as $item) {
            $level1_menu = [
                'name'  => $item['name'],
                'value' => $item['id'],
            ];

            foreach ($level1_sub_menu as $sub_menu) {
                if ($sub_menu['parent_id'] == $item['id']) {
                    $level1_menu['submenu'][] = $sub_menu;
                }
            }
            $result[] = $level1_menu;
        }

        return $result;
    }

    /**
     * 获取子分类id
     *
     * @param $cate_id
     * @return array
     */
    public static function getChildId($cate_id)
    {
        if (!$cate_id || is_nan($cate_id)) {
            return [];
        }

        $prefix     = Config::get('database.prefix');
        $table_name = "{$prefix}exam_cate";
        $ids        = Db::query("
        SELECT id FROM {$table_name} WHERE parent_id = $cate_id
        UNION 
        SELECT id FROM {$table_name} WHERE parent_id IN (SELECT id FROM {$table_name} WHERE parent_id = $cate_id)
        ");

        return $ids ? array_column($ids, 'id') : [];
    }

    /**
     * 组装三级分类格式
     *
     * @param string $kind
     * @param string $type
     * @param int    $subject_id
     * @return array[]
     */
    public static function threeLevel2(string $kind, $type = '', $subject_id = 0)
    {
        $data = self::all(function ($query) use ($kind, $type, $subject_id) {
            if ($subject_id) {
                $query = $query->where('subject_id', $subject_id);
            }
            $query->where('status', '1')->where('kind', $kind)->order('sort desc');
            if ($type == 'look') {
                $query->where('is_look', 1);
            } else if ($type == 'train') {
                $query->where('is_train', 1);
            }
        });

        $result = [];
        $level1 = [];
        $level2 = [];
        $level3 = [];

        // 分级
        foreach ($data as $item) {
            switch ($item['level']) {
                case 1:
                    array_push($level1, $item);
                    break;
                case 2:
                    array_push($level2, $item);
                    break;
                case 3:
                    array_push($level3, $item);
                    break;
            }
        }

        // 先组装level2和level3的数据，即level1的子级
        $level1_sub_menu = [];
        foreach ($level2 as $item2) {
            $children = [];
            foreach ($level3 as $item3) {
                if ($item3['parent_id'] == $item2['id']) {
                    $children[] = [
                        'text'  => $item3['name'] . self::memberSuffix($item3),
                        'value' => $item3['id'],
                    ];
                }
            }

            $level1_sub_menu[] = [
                'text'      => $item2['name'] . self::memberSuffix($item2),
                'value'     => $item2['id'],
                'children'  => $children,
                'parent_id' => $item2['parent_id'],
            ];
        }

        // 装载level1
        foreach ($level1 as $item) {
            $level1_menu = [
                'text'  => $item['name'] . self::memberSuffix($item),
                'value' => $item['id'],
            ];

            foreach ($level1_sub_menu as $children) {
                if ($children['parent_id'] == $item['id']) {
                    $level1_menu['children'][] = $children;
                }
            }
            $result[] = $level1_menu;
        }

        return $result;
    }

    /**
     * 会员标识后缀
     *
     * @param $cate
     * @return string
     */
    public static function memberSuffix($cate)
    {
        if ($cate['uses'] == CateUses::ONLY_MEMBER) {
            return '（仅会员可用）';
        }
        return '';
    }

    /**
     * 获取顶级分类
     *
     * @param $cate_id
     * @return self
     */
    public static function getTop($cate_id)
    {
        if (!$cate_id || !is_numeric($cate_id)) {
            return null;
        }

        $cate = self::get($cate_id);
        if (!$cate) {
            return null;
        }
        if (!$cate['parent_id']) {
            return $cate;
        }

        return self::getTop($cate['parent_id']);
    }

    /**
     * 获取分类上级名称
     *
     * @param $cate_id
     * @return array
     */
    public static function getFullCates($cate_id)
    {
        $cate       = self::get($cate_id);
        $cate_names = $cate['name'];
        $cate_ids   = $cate_id;
        if ($cate['parent_id'] > 0) {
            $parent_cate = CateModel::get($cate['parent_id']);
            $cate_names  = $parent_cate['name'] . '-' . $cate_names;
            $cate_ids    = $parent_cate['id'] . ',' . $cate_ids;
            if ($parent_cate['parent_id'] > 0) {
                $top_parent_cate = CateModel::get($parent_cate['parent_id']);
                $cate_names      = $top_parent_cate['name'] . '-' . $cate_names;
                $cate_ids        = $top_parent_cate['id'] . ',' . $cate_ids;
            }
        }

        return [
            'cate_names' => $cate_names,
            'cate_ids'   => $cate_ids,
        ];
    }

    /**
     * 验证分类是否在指定分类下
     *
     * @param $cate_ids
     * @param $cate_id
     * @return bool
     */
    public static function isInCateIds($cate_ids, $cate_id)
    {
        if (!$cate_ids || !$cate_id) {
            return false;
        }

        $cate_ids    = explode(',', $cate_ids);
        $cates       = self::where('id', 'in', $cate_ids)->select();
        $cate_id_arr = [];
        // dd($cate_ids, $cate_id, $cates);

        foreach ($cates as $cate) {
            if ($cate['level'] == 3) {
                $cate_id_arr[] = $cate['id'];
            } else {
                $cate_id_arr[] = $cate['id'];
                $cate_id_arr   = array_merge($cate_id_arr, self::getChildId($cate['id']));
            }
        }

        return in_array($cate_id, $cate_id_arr);
    }

    /**
     * 验证用户是否有题库权限
     *
     * @param $cate_id
     * @param $user_id
     * @return bool|void
     * @throws \think\exception\DbException
     */
    public static function checkUserHasCatePermission($cate_id, $user_id)
    {
        $cate = CateModel::get($cate_id);
        if (!$cate) {
            exam_fail('题库不存在');
        }
        if (intval($cate['status']) != 1) {
            exam_fail('题库已关闭');
        }

        // 题库需要付费开通
        if (!$cate['is_free'] && $cate['price'] > 0) {
            if (CateUserLogModel::isOpenCate($user_id, $cate_id)) {
                return true;
            } else {
                exam_fail('该题库需要付费开通，请先购买后再试', ['need_open' => true, 'cate' => $cate]);
            }
        }

        // 题库仅限会员可用
        if ($cate['uses'] == CateUses::ONLY_MEMBER) {// && !UserModel::isMember($user_id)
            if (!UserModel::isMember($user_id)) {
                exam_fail('该题库仅限会员可用，请开通会员后再试', ['need_open_member' => true]);
            }

            $user_info = UserInfoModel::getUserInfo($user_id);
            if ($user_info && $user_info['member_config_id']) {
                $member_config = MemberConfigModel::get($user_info['member_config_id']);
                // 会员配置是否可用当前题库
                if ($member_config && $member_config['uses'] == 'cate' && $member_config['cate_ids']) {
                    if ($member_config['cate_ids'] == $cate_id) {
                        return true;
                    }

                    if (!\addons\exam\model\CateModel::isInCateIds($member_config['cate_ids'], $cate_id)) {
                        exam_fail('当前会员无法使用该题库，请开通其他会员后再试', ['need_open_member' => true]);
                    }
                }
            }
        }
    }

    /**
     * 验证用户是否有试卷分类权限
     *
     * @param $cate_id
     * @param $user_id
     * @return bool|void
     * @throws \think\exception\DbException
     */
    public static function checkUserHasPaperCatePermission($cate_id, $user_id)
    {
        $cate = CateModel::get($cate_id);
        if (!$cate) {
            exam_fail('试卷分类不存在');
        }
        // if (intval($cate['status']) != 1) {
        //     fail('试卷分类已关闭');
        // }

        // 题库需要付费开通
        // if (!$cate['is_free'] && $cate['price'] > 0) {
        //     if (CateUserLogModel::isOpenCate($user_id, $cate_id)) {
        //         return true;
        //     } else {
        //         fail('该题库需要付费开通，请先购买后再试', ['need_open' => true, 'cate' => $cate]);
        //     }
        // }

        // 题库仅限会员可用
        if ($cate['uses'] == CateUses::ONLY_MEMBER) {// && !UserModel::isMember($user_id)
            if (!UserModel::isMember($user_id)) {
                // fail('该试卷分类仅限会员可用，请开通会员后再试', ['need_open_member' => true]);
                return false;
            }

            $user_info = UserInfoModel::getUserInfo($user_id);
            if ($user_info && $user_info['member_config_id']) {
                $member_config = MemberConfigModel::get($user_info['member_config_id']);
                // 会员配置是否可用当前试卷分类
                if ($member_config && $member_config['paper_uses'] == 'part_cate' && $member_config['paper_cate_ids']) {
                    if ($member_config['paper_cate_ids'] == $cate_id) {
                        return true;
                    }

                    // dd($member_config['paper_cate_ids'], $cate_id, \addons\exam\model\CateModel::isInCateIds($member_config['paper_cate_ids'], $cate_id));
                    if (!\addons\exam\model\CateModel::isInCateIds($member_config['paper_cate_ids'], $cate_id)) {
                        // fail('当前会员无法使用该题库，请开通其他会员后再试', ['need_open_member' => true]);
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
