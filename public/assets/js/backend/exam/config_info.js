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

                Controller.api.bindPageMyFollowMpBtn();
                Controller.api.bindPageMyFollowMpClick();
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
        }
    };
    return Controller;
});
