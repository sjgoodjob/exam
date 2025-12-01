<?php

namespace addons\exam\controller;

use app\common\controller\Api;
use think\Lang;

class Base extends Api
{
    public function _initialize()
    {
        parent::_initialize();

        $this->loadCommonFile();

        $controller = strtolower($this->request->controller());
        $this->loadlang($controller);

        $this->getAppVersion();
    }

    /**
     * 加载公共函数库文件
     */
    protected function loadCommonFile()
    {
        require_once ROOT_PATH . 'addons/exam/helper.php';
    }

    /**
     * 加载后台语言包
     *
     * @param string $name
     */
    protected function loadlang($name)
    {
        $lang = $this->request->langset();
        $lang = preg_match("/^([a-zA-Z\-_]{2,10})\$/i", $lang) ? $lang : 'zh-cn';
        Lang::load(APP_PATH . '/admin/lang/' . $lang . '/exam/' . str_replace('.', '/', $name) . '.php');
    }

    /**
     * 加载用户信息
     */
    protected function loadUserData()
    {
        if (!$this->auth->isLogin()) {
            return;
        }

    }

    /**
     * 接口执行后统一的返回格式
     *
     * @param Closure $closure
     * @param string  $error_msg
     * @param array   $success_data 带return_result时返回结果给前端
     * @return array|void
     */
    protected function operateResult(\Closure $closure, string $error_msg = '操作失败，请重试', array $success_data = [])
    {
        if ($result = $closure()) {
            if ($success_data && isset($success_data['return_result'])) {
                exam_succ($result);
            }
            exam_succ($success_data);
        }

        exam_fail($error_msg);
    }

    /**
     * 获取前端版本号
     *
     * @return int
     */
    protected function getAppVersion()
    {
        $app_version = $this->request->header('app-version', '1.0.0');
        $app_version = str_replace('.', '', $app_version);
        $app_version = is_numeric($app_version) ? intval($app_version) : 100;

        if (!defined('APP_VERSION')) {
            define('APP_VERSION', $app_version);
        }

        return $app_version;
    }

}
