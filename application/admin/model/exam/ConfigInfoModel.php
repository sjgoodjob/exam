<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;


class ConfigInfoModel extends BaseModel
{
    // 表名
    protected $name = 'exam_config_info';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append
        = [

        ];

    /**
     * 获取配置
     * @return ConfigInfoModel|null
     * @throws \think\exception\DbException
     */
    public static function getOne()
    {
        $configs = self::all();
        if ($configs) {
            return $configs[0];
        }
        return null;
    }

    /**
     * 获取配置
     * @param string  $field   配置组名
     * @param string  $key     字段
     * @param string  $default 字段默认值
     * @param boolean $refresh 是否刷新缓存
     * @return mixed
     */
    public static function getConfigInfo(string $field, $key = '', $default = '', $refresh = true)
    {
        $config = \think\Cache::get($field);
        if (!$config || $refresh) {
            $config = \think\Db::name('exam_config_info')->order('id')->limit(1)->value($field);
            if (!$config) {
                return null;
            }

            $config = json_decode($config, true);
            //存入缓存
            \think\Cache::set($field, $config, 20);
        }

        if ($key) {
            return $config[$key] ?? $default;
        }

        return $config;
    }

    /**
     * 获取H5地址
     * @return mixed|string|null
     */
    public static function getH5Url()
    {
        $default = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/h5/#/';
        return ConfigInfoModel::getConfigInfo('system_config', 'h5_url', $default);
    }
}
