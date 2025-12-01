<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;
use think\Config;
use think\Db;
use traits\model\SoftDelete;


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
            'level_text'
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
     * @param string $kind
     * @return array[]
     */
    public static function threeLevel(string $kind)
    {
        $data = self::all(function ($query) use ($kind) {
            $query->where('status', '1')->where('kind', $kind)->order('sort desc');
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
     * 获取子分类id
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
     * @param string $kind
     * @return array[]
     */
    public static function threeLevel2(string $kind)
    {
        $data = self::all(function ($query) use ($kind) {
            $query->where('status', '1')->where('kind', $kind)->order('sort desc');
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
                        'text'  => $item3['name'],
                        'value' => $item3['id'],
                    ];
                }
            }

            $level1_sub_menu[] = [
                'text'      => $item2['name'],
                'value'     => $item2['id'],
                'children'  => $children,
                'parent_id' => $item2['parent_id']
            ];
        }

        // 装载level1
        foreach ($level1 as $item) {
            $level1_menu = [
                'text'  => $item['name'],
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
     * 获取顶级分类
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
}
