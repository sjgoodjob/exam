<?php

namespace addons\exam;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Exam extends Addons
{
    protected $menu = [
        [
            "name"    => "exam",
            "title"   => "考试系统",
            "icon"    => "fa fa fa-columns",
            "sublist" => [
                [
                    "name"    => "exam/dashboard/index",
                    "title"   => "控制台",
                    "icon"    => "fa fa-dashboard",
                    "weigh"   => 199,
                    "sublist" => [],
                ],
                [
                    "name"    => "exam/config_info",
                    "title"   => "参数配置",
                    "icon"    => "fa fa-cogs",
                    "weigh"   => 101,
                    "sublist" => [
                        [
                            "name"  => "exam/config_info/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/config_info/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/config_info/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/config_info/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/config_info/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/slide",
                    "title"   => "轮播图",
                    "icon"    => "fa fa-image",
                    "weigh"   => 100,
                    "sublist" => [
                        [
                            "name"  => "exam/slide/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/slide/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/slide/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/slide/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/slide/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/notice",
                    "title"   => "系统公告",
                    "icon"    => "fa fa-volume-up",
                    "weigh"   => 100,
                    "sublist" => [
                        [
                            "name"  => "exam/notice/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/notice/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/notice/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/notice/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/notice/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/news",
                    "title"   => "学习动态",
                    "icon"    => "fa fa-map-o",
                    "weigh"   => 99,
                    "sublist" => [
                        [
                            "name"  => "exam/news/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/news/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/news/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/news/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/news/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/subject",
                    "title"   => "科目管理",
                    "icon"    => "fa fa-map-o",
                    "weigh"   => 99,
                    "sublist" => [
                        [
                            "name"  => "exam/subject/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/subject/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/subject/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/subject/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/subject/multi",
                            "title" => "批量更新",
                        ],
                        [
                            "name"  => "exam/subject/recyclebin",
                            "title" => "回收站",
                        ],
                        [
                            "name"  => "exam/subject/destroy",
                            "title" => "真实删除",
                        ],
                        [
                            "name"  => "exam/subject/restore",
                            "title" => "还原",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/cate",
                    "title"   => "分类管理",
                    "icon"    => "fa fa-align-left",
                    "weigh"   => 98,
                    "sublist" => [
                        [
                            "name"  => "exam/cate/recyclebin",
                            "title" => "回收站",
                        ],
                        [
                            "name"  => "exam/cate/destroy",
                            "title" => "真实删除",
                        ],
                        [
                            "name"  => "exam/cate/restore",
                            "title" => "还原",
                        ],
                        [
                            "name"  => "exam/cate/import",
                            "title" => "Import",
                        ],
                        [
                            "name"  => "exam/cate/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/cate/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/cate/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/cate/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/cate/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/question",
                    "title"   => "试题管理",
                    "icon"    => "fa fa-question-circle-o",
                    "weigh"   => 90,
                    "sublist" => [
                        [
                            "name"  => "exam/question/recyclebin",
                            "title" => "回收站",
                        ],
                        [
                            "name"  => "exam/question/destroy",
                            "title" => "真实删除",
                        ],
                        [
                            "name"  => "exam/question/restore",
                            "title" => "还原",
                        ],
                        [
                            "name"  => "exam/question/import",
                            "title" => "导入",
                        ],
                        [
                            "name"  => "exam/question/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/question/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/question/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/question/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/question/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/paper",
                    "title"   => "试卷管理",
                    "icon"    => "fa fa-newspaper-o",
                    "weigh"   => 80,
                    "sublist" => [
                        [
                            "name"  => "exam/paper/recyclebin",
                            "title" => "回收站",
                        ],
                        [
                            "name"  => "exam/paper/destroy",
                            "title" => "真实删除",
                        ],
                        [
                            "name"  => "exam/paper/restore",
                            "title" => "还原",
                        ],
                        [
                            "name"  => "exam/paper/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/paper/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/paper/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/paper/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/paper/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/grade",
                    "title"   => "考试成绩",
                    "icon"    => "fa fa-list",
                    "weigh"   => 79,
                    "sublist" => [
                        [
                            "name"  => "exam/grade/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/grade/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/grade/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/grade/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/grade/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/roommanage",
                    "title"   => "考场功能",
                    "icon"    => "fa fa-home",
                    "weigh"   => 75,
                    "sublist" => [
                        [
                            "name"    => "exam/room",
                            "title"   => "考场管理",
                            "icon"    => "fa fa-home",
                            "weigh"   => 75,
                            "sublist" => [
                                [
                                    "name"  => "exam/room/recyclebin",
                                    "title" => "回收站",
                                ],
                                [
                                    "name"  => "exam/room/destroy",
                                    "title" => "真实删除",
                                ],
                                [
                                    "name"  => "exam/room/restore",
                                    "title" => "还原",
                                ],
                                [
                                    "name"  => "exam/room/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/room/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/room/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/room/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/room/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/room_signup",
                            "title"   => "考场报名",
                            "icon"    => "fa fa-pencil",
                            "weigh"   => 74,
                            "sublist" => [
                                [
                                    "name"  => "exam/room_signup/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/room_signup/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/room_signup/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/room_signup/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/room_signup/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/room_grade",
                            "title"   => "考场考试成绩",
                            "icon"    => "fa fa-list-ol",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/room_grade/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/room_grade/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/room_grade/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/room_grade/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/room_grade/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    "name"    => "exam/cert_manage",
                    "title"   => "证书管理",
                    "icon"    => "fa fa-certificate",
                    "weigh"   => 70,
                    "sublist" => [
                        [
                            "name"    => "exam/cert_config",
                            "title"   => "证书配置",
                            "icon"    => "fa fa-cog",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/cert_config/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/cert_config/recyclebin",
                                    "title" => "回收站",
                                ],
                                [
                                    "name"  => "exam/cert_config/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/cert_config/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/cert_config/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/cert_config/destroy",
                                    "title" => "真实删除",
                                ],
                                [
                                    "name"  => "exam/cert_config/restore",
                                    "title" => "还原",
                                ],
                                [
                                    "name"  => "exam/cert_config/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/cert",
                            "title"   => "证书管理",
                            "icon"    => "fa fa-certificate",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/cert/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/cert/recyclebin",
                                    "title" => "回收站",
                                ],
                                [
                                    "name"  => "exam/cert/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/cert/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/cert/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/cert/destroy",
                                    "title" => "真实删除",
                                ],
                                [
                                    "name"  => "exam/cert/restore",
                                    "title" => "还原",
                                ],
                                [
                                    "name"  => "exam/cert/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    "name"    => "exam/school",
                    "title"   => "学校管理",
                    "icon"    => "fa fa-building-o",
                    "weigh"   => 69,
                    "sublist" => [
                        [
                            "name"  => "exam/school/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/school/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/school/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/school/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/school/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
                [
                    "name"    => "exam/member",
                    "title"   => "会员管理",
                    "icon"    => "fa fa-user-circle",
                    "weigh"   => 60,
                    "sublist" => [
                        [
                            "name"    => "exam/user_info",
                            "title"   => "用户信息",
                            "icon"    => "fa fa-user-circle",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/user_info/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/user_info/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/user_info/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/user_info/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/user_info/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/member_config",
                            "title"   => "会员开通配置",
                            "icon"    => "fa fa-cog",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/member_config/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/member_config/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/member_config/recyclebin",
                                    "title" => "回收站",
                                ],
                                [
                                    "name"  => "exam/member_config/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/member_config/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/member_config/destroy",
                                    "title" => "真实删除",
                                ],
                                [
                                    "name"  => "exam/member_config/restore",
                                    "title" => "还原",
                                ],
                                [
                                    "name"  => "exam/member_config/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/member_code",
                            "title"   => "会员激活码",
                            "icon"    => "fa fa-barcode",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/member_code/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/member_code/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/member_code/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/member_code/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/member_code/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    "name"    => "exam/score",
                    "title"   => "积分管理",
                    "icon"    => "fa fa-support",
                    "weigh"   => 59,
                    "sublist" => [
                        [
                            "name"    => "exam/user_score_log",
                            "title"   => "用户积分变动",
                            "icon"    => "fa fa-list-alt",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/user_score_log/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/user_score_log/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/user_score_log/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/user_score_log/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/user_score_log/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/score_good",
                            "title"   => "积分商品",
                            "icon"    => "fa fa-shopping-bag",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/score_good/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/score_good/recyclebin",
                                    "title" => "回收站",
                                ],
                                [
                                    "name"  => "exam/score_good/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/score_good/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/score_good/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/score_good/destroy",
                                    "title" => "真实删除",
                                ],
                                [
                                    "name"  => "exam/score_good/restore",
                                    "title" => "还原",
                                ],
                                [
                                    "name"  => "exam/score_good/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/score_good_order",
                            "title"   => "积分商品兑换单",
                            "icon"    => "fa fa-wpforms",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/score_good_order/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/score_good_order/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/score_good_order/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/score_good_order/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/score_good_order/multi",
                                    "title" => "批量更新",
                                ],
                                [
                                    "name"  => "exam/score_good_order/ship",
                                    "title" => "发货",
                                ],
                                [
                                    "name"  => "exam/score_good_order/complete",
                                    "title" => "完成",
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    "name"    => "exam/orders",
                    "title"   => "订单管理",
                    "icon"    => "fa fa-list-ul",
                    "weigh"   => 58,
                    "sublist" => [
                        [
                            "name"    => "exam/pay_log",
                            "title"   => "支付记录",
                            "icon"    => "fa fa-credit-card",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/pay_log/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/pay_log/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/pay_log/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/pay_log/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/pay_log/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/member_order",
                            "title"   => "开通会员订单",
                            "icon"    => "fa fa-address-card-o",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/member_order/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/member_order/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/member_order/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/member_order/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/member_order/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/paper_order",
                            "title"   => "试卷考试支付订单",
                            "icon"    => "fa fa-list",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/paper_order/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/paper_order/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/paper_order/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/paper_order/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/paper_order/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/cate_order",
                            "title"   => "题库支付订单",
                            "icon"    => "fa fa-align-left",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/cate_order/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/cate_order/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/cate_order/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/cate_order/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/cate_order/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    "name"    => "exam/cate_active",
                    "title"   => "题库激活",
                    "icon"    => "fa fa-list-alt",
                    "weigh"   => 57,
                    "sublist" => [
                        [
                            "name"    => "exam/cate_code",
                            "title"   => "题库激活码",
                            "icon"    => "fa fa-barcode",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/cate_code/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/cate_code/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/cate_code/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/cate_code/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/cate_code/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/cate_user_log",
                            "title"   => "用户激活题库记录",
                            "icon"    => "fa fa-list-alt",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/cate_user_log/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/cate_user_log/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/cate_user_log/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/cate_user_log/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/cate_user_log/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    "name"    => "exam/correction",
                    "title"   => "纠错反馈",
                    "icon"    => "fa fa-wpforms",
                    "weigh"   => 56,
                    "sublist" => [
                        [
                            "name"    => "exam/correction_type",
                            "title"   => "纠错反馈类型",
                            "icon"    => "fa fa-dot-circle-o",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/correction_type/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/correction_type/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/correction_type/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/correction_type/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/correction_type/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                        [
                            "name"    => "exam/correction_question",
                            "title"   => "纠错反馈试题",
                            "icon"    => "fa fa-wpforms",
                            "weigh"   => 0,
                            "sublist" => [
                                [
                                    "name"  => "exam/correction_question/index",
                                    "title" => "查看",
                                ],
                                [
                                    "name"  => "exam/correction_question/add",
                                    "title" => "添加",
                                ],
                                [
                                    "name"  => "exam/correction_question/edit",
                                    "title" => "编辑",
                                ],
                                [
                                    "name"  => "exam/correction_question/del",
                                    "title" => "删除",
                                ],
                                [
                                    "name"  => "exam/correction_question/multi",
                                    "title" => "批量更新",
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    "name"    => "exam/friend_apps",
                    "title"   => "友情小程序",
                    "icon"    => "fa fa-handshake-o",
                    "weigh"   => 0,
                    "sublist" => [
                        [
                            "name"  => "exam/friend_apps/index",
                            "title" => "查看",
                        ],
                        [
                            "name"  => "exam/friend_apps/add",
                            "title" => "添加",
                        ],
                        [
                            "name"  => "exam/friend_apps/edit",
                            "title" => "编辑",
                        ],
                        [
                            "name"  => "exam/friend_apps/del",
                            "title" => "删除",
                        ],
                        [
                            "name"  => "exam/friend_apps/multi",
                            "title" => "批量更新",
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * 插件安装方法
     *
     * @return bool
     */
    public function install()
    {
        Menu::create($this->menu);
        return true;
    }

    /**
     * 插件卸载方法
     *
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('exam');
        return true;
    }

    /**
     * 插件启用方法
     *
     * @return bool
     */
    public function enable()
    {
        Menu::enable('exam');
        return true;
    }

    /**
     * 插件禁用方法
     *
     * @return bool
     */
    public function disable()
    {
        Menu::disable('exam');
        return true;
    }

    /**
     * 插件升级方法
     *
     * @return bool
     */
    public function upgrade()
    {
        // 如果菜单有变更则升级菜单
        Menu::upgrade('exam', $this->menu);
        return true;
    }

}
