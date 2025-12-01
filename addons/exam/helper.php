<?php

if (!function_exists('exam_dd')) {
    function exam_dd($obj)
    {
        halt($obj);
    }
}

if (!function_exists('exam_fail')) {
    /**
     * 主动抛错
     */
    function exam_fail($message, $data = [], $code = 0)
    {
        $result = [
            'code' => $code,
            'data' => $data,
            'msg'  => is_array($message) ? json_encode($message) : $message,
        ];

        // 如果未设置类型则自动判断
        $type     = 'json';
        $response = \think\Response::create($result, $type, 200);

        throw new \think\exception\HttpResponseException($response);
    }
}

if (!function_exists('exam_succ')) {
    /**
     * 成功返回
     */
    function exam_succ($data = [], $message = '')
    {
        $result = [
            'code' => 1,
            'data' => $data,
            'msg'  => $message,
        ];

        // 如果未设置类型则自动判断
        $type     = 'json';
        $response = \think\Response::create($result, $type, 200);

        throw new \think\exception\HttpResponseException($response);
    }
}

if (!function_exists('exam_getConfig')) {
    /**
     * 获取配置
     *
     * @param string  $field   配置组名
     * @param string  $key     字段
     * @param string  $default 字段默认值
     * @param boolean $refresh 是否刷新缓存
     * @return mixed
     */
    function exam_getConfig(string $field, $key = '', $default = '', $refresh = true)
    {
        $config = \think\Cache::get($field);
        if (!$config || $refresh) {
            $config = \think\Db::name('exam_config_info')->order('id')->limit(1)->value($field);
            if (!$config) {
                return null;
            }

            $config = json_decode($config, true);
            //存入缓存
            \think\Cache::set($field, $config);
        }

        if ($key) {
            return $config[$key] ?? $default;
        }

        return $config;
    }
}

if (!function_exists('exam_getCurl')) {
    /**
     * get请求
     *
     * @param $url
     * @return bool|string
     */
    function exam_getCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}

if (!function_exists('exam_postCurl')) {
    /**
     * post请求
     *
     * @param        $url
     * @param string $data
     * @param string $type
     * @return bool|string
     */
    function exam_postCurl($url, $data = '', $type = 'json')
    {
        if ($type == 'json') {
            $data   = json_encode($data); //对数组进行json编码
            $header = ["Content-type: application/json;charset=UTF-8", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache"];
        } else {
            $header = [
                "Content-type: application/x-www-form-urlencoded;charset=UTF-8",
                "Accept: application/json",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
            ];
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $res = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error+' . curl_error($curl);
        }
        curl_close($curl);
        return $res;
    }
}

if (!function_exists('exam_only_keys')) {
    /**
     * 只取数组部分key数据
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    function exam_only_keys(array $array, array $keys)
    {
        $result = [];
        foreach ($array as $k => $value) {
            if (in_array($k, $keys)) {
                $result[$k] = $value;
            }
        }
        return $result;
    }
}

if (!function_exists('exam_hidden_keys')) {
    /**
     * 隐藏数组部分key数据
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    function exam_hidden_keys(array $array, array $keys)
    {
        $result = [];
        foreach ($array as $k => $value) {
            if (in_array($k, $keys)) {
                unset($value[$k]);
                $result[$k] = $value;
            }
        }
        return $result;
    }
}

if (!function_exists('exam_hidden_list_keys')) {
    /**
     * 隐藏数组部分key数据
     *
     * @param array $list
     * @param array $keys
     * @return array
     */
    function exam_hidden_list_keys(array $list, array $keys)
    {
        $list   = collection($list)->toArray();
        $result = [];
        foreach ($list as $i => $item) {
            foreach ($item as $k => $value) {
                if (in_array($k, $keys)) {
                    unset($item[$k]);
                }
            }
            $result[$i] = $item;
        }
        return $result;
    }
}

if (!function_exists('exam_is_empty_in_array')) {
    /**
     * 数组内是否包含且存在字段值
     *
     * @param $array
     * @param $field
     * @return bool
     */
    function exam_is_empty_in_array($array, $field)
    {
        if (!isset($array[$field]) || !$array[$field]) {
            return true;
        }

        return false;
    }
}

if (!function_exists('exam_cache_data')) {
    /**
     * 获取/设置缓存数据
     *
     * @param string  $cache_key   缓存key名
     * @param Closure $fun         用户函数，获取并返回数据
     * @param int     $expire_time 缓存过期时间
     * @return mixed
     */
    function exam_cache_data(string $cache_key, Closure $fun, int $expire_time = 0, bool $refresh = false)
    {
        // 固定前缀
        $cache_key = "exam:{$cache_key}";

        // 存在缓存，返回缓存
        if (!$refresh && $cache = cache($cache_key)) {
            return $cache;
        }

        // 执行数据获取
        $data = $fun();
        $data = is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;

        // 设置缓存
        cache($cache_key, $data, $expire_time);
        return $data;
    }
}

if (!function_exists('exam_getUser')) {
    /**
     * 获取Api用户信息
     *
     * @return mixed
     */
    function exam_getUser()
    {
        if (\app\common\library\Auth::instance()->isLogin()) {
            return \app\common\library\Auth::instance();
        }

        return null;
    }
}

if (!function_exists('exam_getUserId')) {
    /**
     * 获取Api用户ID
     *
     * @return mixed
     */
    function exam_getUserId()
    {
        if ($user = exam_getUser()) {
            return $user->id;
        }

        return 0;
    }
}

if (!function_exists('exam_generate_no')) {
    /**
     * 根据时间生成编号
     *
     * @return string
     */
    function exam_generate_no($pre = '')
    {
        $date         = date('YmdHis', time());
        $u_timestamp  = microtime(true);
        $timestamp    = floor($u_timestamp);
        $milliseconds = round(($u_timestamp - $timestamp) * 100); // 改这里的数值控制毫秒位数
        return $pre . $date . date(preg_replace('`(?<!\\\\)u`', $milliseconds, 'u'), $timestamp);
    }
}

if (!function_exists('exam_str_trim')) {
    /**
     * 字符串去除空格
     *
     * @return string
     */
    function exam_str_trim($str)
    {
        return str_replace(' ', '', $str);
    }
}

if (!function_exists('exam_formatHour')) {
    /**
     * 秒数转换为时分秒
     *
     * @param $duration
     * @return string
     */
    function exam_formatHour($duration)
    {
        $hour = floor($duration / 3600);
        $min  = floor(($duration - $hour * 3600) / 60);
        $sec  = floor($duration - $hour * 3600 - $min * 60);

        return sprintf("%02d:%02d:%02d", $hour, $min, $sec);
    }
}

if (!function_exists('exam_antiRepeat')) {
    /**
     * 通用接口防抖函数
     */
    function exam_antiRepeat($unique_id = false, $time = 0, $msg = '请勿频繁操作')
    {
        $auth = \app\common\library\Auth::instance();
        if (!$unique_id && !$auth->isLogin()) {
            exam_fail('该方法使用“域名+模块+控制器+方法名称+用户ID”作为唯一key值，无法使用auth()->id()的接口请传入unique_id参数代替 用户ID');
        }

        // 模块名
        $modules = request()->module();
        // 控制器名称
        $controller = request()->controller();
        // 方法名称
        $method = request()->action();
        // 传入$unique_id则优先使用$unique_id
        if ($unique_id) {
            $key = $_SERVER['HTTP_HOST'] . '-antiRepeat-' . $modules . '.' . $controller . '.' . $method . '.' . $unique_id;
        } else {
            $key = $_SERVER['HTTP_HOST'] . '-antiRepeat-' . $modules . '.' . $controller . '.' . $method . '.' . $auth->id;
        }

        $cache = \think\Cache::store('file');
        if ($time) {
            // 访问进行中
            if ($cache->has($key) && $cache->get($key) == 1) {
                exam_fail($msg);
            } else {
                $cache->set($key, 1, $time);
            }
        } else {
            // 访问进行中
            if ($cache->get($key) == 1) {
                exam_fail($msg);
            } else {
                $cache->set($key, 1);
                register_shutdown_function(function () use ($key, $cache) {
                    $cache->set($key, 0);
                });
            }
        }
    }
}

