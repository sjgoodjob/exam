define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/user_info/index' + location.search,
                    add_url: 'exam/user_info/add',
                    edit_url: 'exam/user_info/edit',
                    // del_url: 'exam/user_info/del',
                    // multi_url: 'exam/user_info/multi',
                    // import_url: 'exam/user_info/import',
                    table: 'exam_user_info',
                }
            });

            var table = $("#table");

            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);

                // $("input[name='user_id']", form).addClass("selectpage").data("source", "user/user/index").data("field", "nickname");
                $("input[name='member_config_id']", form).addClass("selectpage").data("source", "exam/member_config/index").data("field", "name");

                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        {field: 'user.mobile', title: __('User.mobile'), operate: 'LIKE'},
                        // {field: 'type', title: __('Type'), searchList: {"NORMAL":__('Type normal'),"VIP_MONTH":__('Type vip_month'),"VIP_YEAR":__('Type vip_year'),"VIP_LIFE":__('Type vip_life')}, formatter: Table.api.formatter.normal},
                        {field: 'member_config_id', title: __('会员类型'), visible: false},
                        {field: 'member_config.name', title: __('会员类型'), operate: false},
                        {field: 'score', title: __('Score')},
                        {field: 'expire_time', title: __('Expire_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            align: 'left',
                            buttons: [
                                {
                                    name: 'manual_member',
                                    text: '手动设置会员',
                                    title: '手动设置会员',
                                    icon: 'fa fa-truck',
                                    classname: 'btn btn-xs btn-info btn-dialog btn-manual_score',
                                    url: 'exam/user_info/manualmember',
                                    callback: function (data) {
                                        $('.btn-refresh').click()
                                    },
                                },

                                {
                                    name: 'manual_score',
                                    text: '手动操作积分',
                                    title: '手动操作积分',
                                    icon: 'fa fa-check-circle',
                                    classname: 'btn btn-xs btn-success btn-dialog btn-manual_score',
                                    url: 'exam/user_info/manualscore',
                                    callback: function (data) {
                                        $('.btn-refresh').click()
                                    },
                                },
                            ],
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            $(document).on('click', '.btn-multi-member-set', function () {
                let ids = Table.api.selectedids(table);
                console.log('ids', ids)
                Fast.api.open('exam/user_info/manualmemberset?ids=' + ids.join(','), '批量设置', {ids: ids.join(',')})
            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        manualmember: function () {
            Controller.api.bindevent();
        },
        manualmemberset: function () {
            Controller.api.bindevent();
        },
        manualscore: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
