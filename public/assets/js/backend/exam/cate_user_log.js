define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/cate_user_log/index' + location.search,
                    add_url: 'exam/cate_user_log/add',
                    // edit_url: 'exam/cate_user_log/edit',
                    del_url: 'exam/cate_user_log/del',
                    multi_url: 'exam/cate_user_log/multi',
                    import_url: 'exam/cate_user_log/import',
                    table: 'exam_cate_user_log',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'cate_id', title: __('Cate_id'), visible: false},
                        {
                            field: 'cate.name', title: __('题库'), operate: 'LIKE', formatter: function (value, row, index) {
                                return value + '(' + row.cate_id + ')';
                            }
                        },
                        {field: 'user_id', title: __('User_id'), visible: false},
                        {field: 'user.nickname', title: __('激活用户'), operate: 'LIKE', formatter: function (value, row, index) {
                            return value + '(' + row.user_id + ')';
                        }},
                        {field: 'type', title: __('Type'), searchList: {"PAY":__('Type pay'),"CODE":__('Type code')}, formatter: Table.api.formatter.normal},
                        {field: 'expire_time', title: __('Expire_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
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
