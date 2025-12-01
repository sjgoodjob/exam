define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/cate_code/index' + location.search,
                    add_url: 'exam/cate_code/add',
                    // edit_url: 'exam/cate_code/edit',
                    del_url: 'exam/cate_code/del',
                    multi_url: 'exam/cate_code/multi',
                    import_url: 'exam/cate_code/import',
                    table: 'exam_cate_code',
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
                        {field: 'cate_id', title: __('Cate_id'), visible: false, operate: 'LIKE'},
                        // {
                        //     field: 'cate.name', title: __('题库'), operate: 'LIKE', formatter: function (value, row, index) {
                        //         return value + '(' + row.cate_id + ')';
                        //     }
                        // },
                        {field: 'cate_names', title: __('Cate_id'), operate: false},
                        {field: 'code', title: __('Code'), operate: 'LIKE'},
                        {
                            field: 'days', title: __('days'), operate: false, formatter: function (value, row, index) {
                                if (!value) {
                                    return '永久';
                                }
                                return value + '天';
                            }
                        },
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'user_id', title: __('User_id'), visible: false},
                        {
                            field: 'user.nickname', title: __('激活用户'), operate: false, formatter: function (value, row, index) {
                                if (!row.user.nickname) {
                                    return '-';
                                }
                                return value + '(' + row.user_id + ')';
                            }
                        },
                        {field: 'remark', title: __('remark'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'activate_time', title: __('Activate_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
