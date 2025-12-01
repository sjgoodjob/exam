<?php
/**
 * Created by PhpStorm.
 * User         : zgcLives
 * CreateTime   : 2023/5/3 10:54
 */

namespace addons\exam\library;

class FrontService
{
    /** 小程序页面列表 */
    const PAGES
        = [
            [
                'path'   => '/pages/index/index',
                'name'   => '首页',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/train/index?page=look',
                'name'   => '背题模式',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/train/index?page=train',
                'name'   => '练题模式',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/index/news-list',
                'name'   => '学习动态',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/index/notice-list',
                'name'   => '公告列表',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/search/index',
                'name'   => '题目搜索',
                'params' => [
                    [
                        'field'   => 'keyword',
                        'name'    => '搜索关键词',
                        'type'    => 'string',
                        'require' => false,
                        'value'   => '',
                    ],
                ],
                'module' => '',
            ],
            [
                'path'   => '/pages/paper/index',
                'name'   => '试卷列表',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/paper/paper',
                'name'   => '考试试卷',
                'params' => [
                    [
                        'field'      => 'id',
                        'name'       => '试卷ID',
                        'type'       => 'selectpage',
                        'require'    => true,
                        'value'      => '',
                        'selectpage' => [
                            'source' => 'exam/paper/index',
                            'field'  => 'title',
                            'params' => [],
                        ],
                    ],
                    [
                        'field'      => 'room_id',
                        'name'       => '考场ID',
                        'type'       => 'selectpage',
                        'require'    => false,
                        'value'      => '',
                        'selectpage' => [
                            'source' => 'exam/room/index',
                            'field'  => 'name',
                            'params' => [],
                        ],
                    ],
                ],
                'module' => '',
            ],
            [
                'path'   => '/pages/paper/grade',
                'name'   => '考试记录',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/paper/rank',
                'name'   => '考试排行榜',
                'params' => [
                    [
                        'field'      => 'paper_id',
                        'name'       => '试卷ID',
                        'type'       => 'selectpage',
                        'require'    => true,
                        'value'      => '',
                        'selectpage' => [
                            'source' => 'exam/paper/index',
                            'field'  => 'title',
                            'params' => [],
                        ],
                    ],
                ],
                'module' => '',
            ],
            [
                'path'   => '/pages/room/index',
                'name'   => '考场列表',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/room/detail',
                'name'   => '考场详情',
                'params' => [
                    [
                        'field'      => 'id',
                        'name'       => '考场ID',
                        'type'       => 'selectpage',
                        'require'    => true,
                        'value'      => '',
                        'selectpage' => [
                            'source' => 'exam/room/index',
                            'field'  => 'name',
                            'params' => [],
                        ],
                    ],
                ],
                'module' => '',
            ],
            [
                'path'   => '/pages/room/grade',
                'name'   => '考场成绩',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/room/rank',
                'name'   => '考场排行榜',
                'params' => [
                    [
                        'field'      => 'paper_id',
                        'name'       => '试卷ID',
                        'type'       => 'selectpage',
                        'require'    => true,
                        'value'      => '',
                        'selectpage' => [
                            'source' => 'exam/paper/index',
                            'field'  => 'title',
                            'params' => [],
                        ],
                    ],
                    [
                        'field'      => 'room_id',
                        'name'       => '考场ID',
                        'type'       => 'selectpage',
                        'require'    => true,
                        'value'      => '',
                        'selectpage' => [
                            'source' => 'exam/room/index',
                            'field'  => 'name',
                            'params' => [],
                        ],
                    ],
                ],
                'module' => '',
            ],
            [
                'path'   => '/pages/room/signup-index',
                'name'   => '考场报名记录',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/user/user',
                'name'   => '用户中心',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/user/member-center',
                'name'   => '会员中心',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/user/set',
                'name'   => '个人设置',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/user/my-cate',
                'name'   => '常用题库设置',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/user/login-reg',
                'name'   => '登录注册',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/collect/index',
                'name'   => '我的收藏',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/wrong/index',
                'name'   => '错题记录',
                'params' => [],
                'module' => '',
            ],
            [
                'path'   => '/pages/webview/webview',
                'name'   => '跳转网页',
                'params' => [
                    [
                        'field'   => 'url',
                        'name'    => '网页地址',
                        'type'    => 'string',
                        'require' => true,
                        'value'   => '',
                    ],
                ],
                'module' => '',
            ],
            [
                'path'   => '/pagesSubject/index',
                'name'   => '科目选择',
                'params' => [],
                'module' => '',
            ],

        ];

    /**
     * 获取无参数的页面
     *
     * @return array[]
     */
    public static function getNoParamsPages()
    {
        $pages = self::PAGES;
        return array_filter($pages, function ($item) {
            return empty($item['params']);
        });
    }

    /**
     * 获取小程序跳转路径及参数
     *
     * @return string
     */
    public static function buildUrl($path, $params = [])
    {
        return $path . '?' . http_build_query($params);
    }

    /**
     * 替换内容里的图片CDN链接
     *
     * @param $title
     * @return string
     */
    public static function replaceImgUrl($title)
    {
        $pattern = '/<img.*?src="(.*?)".*?>/i';
        $title   = preg_replace_callback($pattern, function ($matches) {
            $full = $matches[0];
            if (!empty($matches[1])) {
                return str_replace($matches[1], cdnurl($matches[1], true), $full);
            }

            $url      = $matches[1];
            $host     = parse_url($url, PHP_URL_HOST);
            $cdn_host = parse_url(cdnurl('', true), PHP_URL_HOST);
            if ($host) {
                if ($host != $cdn_host) {
                    $url = str_replace($host, $cdn_host, $url);
                }
            } else {
                $url = cdnurl($url, true);
            }

            // ddd($matches);
            return '<img src="' . $url . '">';
        }, $title);
        return $title;
    }
}
