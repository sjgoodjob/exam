<?php

namespace app\admin\model\exam;

use addons\exam\enum\CateUses;
use think\Config;
use think\Db;
use think\Model;
use traits\model\SoftDelete;

class SubjectModel extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'exam_subject';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'level_text',
        'status_text'
    ];


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function getLevelList()
    {
        return ['1' => __('Level 1'), '2' => __('Level 2'), '3' => __('Level 3')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getLevelTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['level']) ? $data['level'] : '');
        $list  = $this->getLevelList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    /**
     * 组装筛选三级科目格式
     * @return array[]
     */
    public static function threeLevel()
    {
        $data = self::all(function ($query) {
            $query->where('status', '1')->order('weigh desc');
        });

        $result = [
            [
                'name'    => '全部',
                'value'   => 'all',
                'submenu' => [
                    'name'  => '全部',
                    'value' => 'all'
                ]
            ]
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
                'parent_id' => $item2['parent_id']
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
     * 获取子科目id
     * @param $subject_id
     * @return array
     */
    public static function getChildId($subject_id)
    {
        if (!$subject_id || is_nan($subject_id)) {
            return [];
        }

        $prefix     = Config::get('database.prefix');
        $table_name = "{$prefix}exam_subject";
        $ids        = Db::query("
        SELECT id FROM {$table_name} WHERE parent_id = $subject_id
        UNION 
        SELECT id FROM {$table_name} WHERE parent_id IN (SELECT id FROM {$table_name} WHERE parent_id = $subject_id)
        ");

        return $ids ? array_column($ids, 'id') : [];
    }

    /**
     * 组装三级科目格式
     * @return array[]
     */
    public static function threeLevel2()
    {
        $data = self::all(function ($query) {
            $query->where('status', '1')->order('weigh desc');
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
                'parent_id' => $item2['parent_id']
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
     * @param $subject
     * @return string
     */
    public static function memberSuffix($subject)
    {
        if (!empty($subject['uses']) && $subject['uses'] == CateUses::ONLY_MEMBER) {
            return '（仅会员可用）';
        }
        return '';
    }

    /**
     * 获取顶级科目
     * @param $subject_id
     * @return self
     */
    public static function getTop($subject_id)
    {
        if (!$subject_id || !is_numeric($subject_id)) {
            return null;
        }

        $subject = self::get($subject_id);
        if (!$subject) {
            return null;
        }
        if (!$subject['parent_id']) {
            return $subject;
        }

        return self::getTop($subject['parent_id']);
    }

    /**
     * 获取科目上级名称
     * @param $subject_id
     * @return array
     */
    public static function getFullCates($subject_id)
    {
        $subject       = self::get($subject_id);
        $subject_names = $subject['name'];
        $subject_ids   = $subject_id;
        if ($subject['parent_id'] > 0) {
            $parent_subject = SubjectModel::get($subject['parent_id']);
            $subject_names  = $parent_subject['name'] . '-' . $subject_names;
            $subject_ids    = $parent_subject['id'] . ',' . $subject_ids;
            if ($parent_subject['parent_id'] > 0) {
                $top_parent_subject = SubjectModel::get($parent_subject['parent_id']);
                $subject_names      = $top_parent_subject['name'] . '-' . $subject_names;
                $subject_ids        = $top_parent_subject['id'] . ',' . $subject_ids;
            }
        }

        return [
            'subject_names' => $subject_names,
            'subject_ids'   => $subject_ids
        ];
    }
}
