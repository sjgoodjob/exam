define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/cate_order/index' + location.search,
                    add_url: 'exam/cate_order/add',
                    edit_url: 'exam/cate_order/edit',
                    del_url: 'exam/cate_order/del',
                    multi_url: 'exam/cate_order/multi',
                    import_url: 'exam/cate_order/import',
                    table: 'exam_cate_order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'user_id', title: __('User_id'), visible: false},
                        {
                            field: 'user.nickname', title: __('用户'), operate: 'LIKE', formatter: function (value, row, index) {
                                return value + '(' + row.user_id + ')';
                            }
                        },
                        {field: 'order_no', title: __('Order_no'), operate: 'LIKE'},
                        {field: 'cate_id', title: __('Cate_id'), visible: false,},
                        {
                            field: 'cate.name', title: __('题库'), operate: 'LIKE', formatter: function (value, row, index) {
                                return value + '(' + row.cate_id + ')';
                            }
                        },
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'pay_money', title: __('Pay_money'), operate:'BETWEEN'},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'days', title: __('days'), operate: false, formatter: function (value, row, index) {
                              if (!value) {
                                  return '永久';
                              }
                              return value + '天';
                            }
                        },
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
