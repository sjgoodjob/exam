define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Controller.api.bindevent();
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        frontend: function () {
            Controller.api.bindevent();
            faForm = Form;
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));

                // 绑定会员开通费用输入框
                $('#c-member_vip_month_fee').blur(function () {
                    Controller.api.clearNoNum($(this));
                });
                $('#c-member_vip_year_fee').blur(function () {
                    Controller.api.clearNoNum($(this));
                });
                $('#c-member_vip_life_fee').blur(function () {
                    Controller.api.clearNoNum($(this));
                });

                Controller.api.bindPageMyFollowMpBtn();
                Controller.api.bindPageMyFollowMpClick();
                Controller.api.bindDiyBtn();
            },
            /**
             * 限制只能输入小数点后两位的数字
             * @param obj
             */
            clearNoNum: function (obj) {
                obj.val(obj.val().replace(/[^\d.]/g, "")); //清除"数字"和"."以外的字符
                obj.val(obj.val().replace(/^\./g, "")); //验证第一个字符是数字而不是
                obj.val(obj.val().replace(/\.{2,}/g, ".")); //只保留第一个. 清除多余的
                obj.val(obj.val().replace(".", "$#$").replace(/\./g, "").replace("$#$", "."));
                obj.val(obj.val().replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3')); //只能输入两个小数

                if (obj.val() === '') {
                    obj.val('0.00');
                }
            },
            /**
             * 绑定我的页面关注按钮事件
             */
            bindPageMyFollowMpBtn: function () {
                var showMyFollowMpBtn = function (page_my_follow_mp_btn) {
                    if (page_my_follow_mp_btn == 1) {
                        $('#page_my_follow_mp_btn-1').show(200);
                    } else {
                        $('#page_my_follow_mp_btn-1').hide(200);
                    }
                };

                var page_my_follow_mp_btn = $('input[name="row[page_my_follow_mp_btn]"]:checked').val();
                showMyFollowMpBtn(page_my_follow_mp_btn);
                $(document).on('click', "input[name='row[page_my_follow_mp_btn]']", function () {
                    let page_my_follow_mp_btn = $(this).val();
                    showMyFollowMpBtn(page_my_follow_mp_btn);
                });
            },

            /**
             * 绑定我的页面关注按钮事件
             */
            bindPageMyFollowMpClick: function () {
                var showMyFollowMpClick = function (page_my_follow_mp_click) {
                    if (page_my_follow_mp_click == 'article') {
                        $('#page_my_follow_mp_click-article').show(200);
                        $('#page_my_follow_mp_click-image').hide(200);
                    } else {
                        $('#page_my_follow_mp_click-article').hide(200);
                        $('#page_my_follow_mp_click-image').show(200);
                    }
                };

                var page_my_follow_mp_click = $('input[name="row[page_my_follow_mp_click]"]:checked').val();
                showMyFollowMpClick(page_my_follow_mp_click);
                $(document).on('click', "input[name='row[page_my_follow_mp_click]']", function () {
                    let page_my_follow_mp_click = $(this).val();
                    showMyFollowMpClick(page_my_follow_mp_click);
                });
            },

            /**
             * 绑定自定义tabbar按钮事件
             */
            bindDiyBtn: function () {
                $(document).on('click', "#btnDiyTabbar", function () {
                    Fast.api.open('exam/diy_tabbar/index', '自定义tabbar', {
                    // Fast.api.open('exam/config_info/diytabbar', '自定义tabbar', {
                        callback: function(value) {
                          // 在这里可以接收弹出层中使用`Fast.api.close(data)`进行回传的数据
                        }
                    });
                });

                $(document).on('click', "#btnDiyIndexButton", function () {
                    let page_style = $('input[name="row[page_index_style]"]:checked').val();
                    console.log('page_style', page_style);
                    Fast.api.open('exam/diy_index_button/index?page_style=' + page_style, '自定义首页按钮', {
                    // Fast.api.open('exam/config_info/diytabbar', '自定义tabbar', {
                        callback: function(value) {
                          // 在这里可以接收弹出层中使用`Fast.api.close(data)`进行回传的数据
                        }
                    });
                });
            }
        }
    };
    return Controller;
});
