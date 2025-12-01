<?php

namespace app\admin\model\exam;

use think\Model;


class DiyIndexButtonModel extends Model
{


    // 表名
    protected $name = 'exam_diy_index_button';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'integer';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'type_text',
        'status_text'
    ];


    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }


    public function getPageStyleList()
    {
        return ['color' => __('page_style color'), 'color2' => __('page_style color2'), 'simple' => __('page_style simple')];
    }

    public function getTypeList()
    {
        return ['icon' => __('Type icon'), 'image' => __('Type image')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['type']) ? $data['type'] : '');
        $list  = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    /**
     * 获取初始化数据
     * @param string $page_style 页面风格
     * @return array|array[]
     */
    public static function getInitDiyData($page_style)
    {
        $data = [];
        if ($page_style == 'color') {
            $data = [
                [
                    'page_style' => 'color',
                    'name'       => '背题模式',
                    'type'       => 'icon',
                    'icon'       => 'eye',
                    'color'      => 'tn-bg-green',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/train/index?page=look',
                    'weigh'      => 1,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'color',
                    'name'       => '练题模式',
                    'type'       => 'icon',
                    'icon'       => 'edit-write',
                    'color'      => 'tn-bg-blue',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/train/index?page=train',
                    'weigh'      => 2,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'color',
                    'name'       => '模拟考试',
                    'type'       => 'icon',
                    'icon'       => 'edit-form',
                    'color'      => 'tn-bg-orange',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/paper/index',
                    'weigh'      => 3,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'color',
                    'name'       => '我的错题',
                    'type'       => 'icon',
                    'icon'       => 'close-circle',
                    'color'      => 'tn-bg-red',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/wrong/index',
                    'weigh'      => 4,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'color',
                    'name'       => '考场报名',
                    'type'       => 'icon',
                    'icon'       => 'empty-data',
                    'color'      => 'tn-bg-cyan',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/room/index',
                    'weigh'      => 4,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'color',
                    'name'       => '题目搜索',
                    'type'       => 'icon',
                    'icon'       => 'search-list',
                    'color'      => 'tn-bg-indigo',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/search/index',
                    'weigh'      => 4,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'color',
                    'name'       => '题目收藏',
                    'type'       => 'icon',
                    'icon'       => 'like-lack',
                    'color'      => 'tn-bg-purple',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/collect/index',
                    'weigh'      => 4,
                    'status'     => '1',
                ],
            ];
        } else if ($page_style == 'color2') {
            $data = [
                [
                    'page_style' => 'color2',
                    'name'       => '背题模式',
                    'type'       => 'icon',
                    'icon'       => 'eye',
                    'color'      => 'tn-bg-green',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/train/index?page=look',
                    'weigh'      => 1,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'color2',
                    'name'       => '练题模式',
                    'type'       => 'icon',
                    'icon'       => 'edit-write',
                    'color'      => 'tn-bg-blue',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/train/index?page=train',
                    'weigh'      => 2,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'color2',
                    'name'       => '模拟考试',
                    'type'       => 'icon',
                    'icon'       => 'edit-form',
                    'color'      => 'tn-bg-orange',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/paper/index',
                    'weigh'      => 3,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'color2',
                    'name'       => '考场报名',
                    'type'       => 'icon',
                    'icon'       => 'empty-data',
                    'color'      => 'tn-bg-cyan',
                    'bg_color'   => '',
                    'image'      => '',
                    'path'       => '/pages/room/index',
                    'weigh'      => 4,
                    'status'     => '1',
                ]
            ];
        } else if ($page_style == 'simple') {
            $data = [
                [
                    'page_style' => 'simple',
                    'name'       => '练题模式',
                    'type'       => 'icon',
                    'icon'       => 'eye',
                    'color'      => 'tn-color-aquablue',
                    'bg_color'   => 'tn-main-gradient-aquablue--light',
                    'image'      => '',
                    'path'       => '/pages/train/index?page=train',
                    'weigh'      => 1,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'simple',
                    'name'       => '背题模式',
                    'type'       => 'icon',
                    'icon'       => 'edit-write',
                    'color'      => 'tn-color-blue',
                    'bg_color'   => 'tn-main-gradient-blue--light',
                    'image'      => '',
                    'path'       => '/pages/train/index?page=look',
                    'weigh'      => 2,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'simple',
                    'name'       => '模拟考试',
                    'type'       => 'icon',
                    'icon'       => 'edit-form',
                    'color'      => 'tn-color-indigo',
                    'bg_color'   => 'tn-main-gradient-indigo--light',
                    'image'      => '',
                    'path'       => '/pages/paper/index',
                    'weigh'      => 3,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'simple',
                    'name'       => '考场报名',
                    'type'       => 'icon',
                    'icon'       => 'empty-data',
                    'color'      => 'tn-color-cyan',
                    'bg_color'   => 'tn-main-gradient-cyan--light',
                    'image'      => '',
                    'path'       => '/pages/room/index',
                    'weigh'      => 4,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'simple',
                    'name'       => '我的错题',
                    'type'       => 'icon',
                    'icon'       => 'close-circle',
                    'color'      => 'tn-color-teal',
                    'bg_color'   => 'tn-main-gradient-teal--light',
                    'image'      => '',
                    'path'       => '/pages/wrong/index',
                    'weigh'      => 5,
                    'status'     => '1',
                ],
                [
                    'page_style' => 'simple',
                    'name'       => '题目收藏',
                    'type'       => 'icon',
                    'icon'       => 'like-lack',
                    'color'      => 'tn-color-green',
                    'bg_color'   => 'tn-main-gradient-green--light',
                    'image'      => '',
                    'path'       => '/pages/collect/index',
                    'weigh'      => 6,
                    'status'     => '1',
                ],
            ];
        }

        return $data;
    }
}
