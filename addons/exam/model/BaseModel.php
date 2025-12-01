<?php


namespace addons\exam\model;

use addons\exam\traits\ModelExtend;
use think\Model;

/** 基础模型 */
class BaseModel extends Model
{
    use ModelExtend;

    protected static function init()
    {
        parent::init();
        self::loadCommonFile();
    }

    /**
     * 加载公共函数库文件
     */
    protected static function loadCommonFile()
    {
        require_once ROOT_PATH . 'addons/exam/helper.php';
    }

    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ?: ($data['createtime'] ?? '');
        return is_numeric($value) ? date('Y-m-d H:i:s', $value) : $value;
    }

    // +----------------------------------------------------------------------
    // 预加载模型关联（仅返回部分数据）
    // +----------------------------------------------------------------------

    public static function withSimpleUser()
    {
        return function ($query) {
            return $query->field('id,nickname,avatar');
        };
    }

    public static function withSimpleCate()
    {
        return function ($query) {
            return $query->field('id,name');
        };
    }

    public static function withSimplePaper()
    {
        return function ($query) {
            return $query->field('id,title');
        };
    }

    public static function withSimpleRoom()
    {
        return function ($query) {
            return $query->field('id,name');
        };
    }
}
