define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/paper_order/index' + location.search,
                    // add_url: 'exam/paper_order/add',
                    // edit_url: 'exam/paper_order/edit',
                    // del_url: 'exam/paper_order/del',
                    // multi_url: 'exam/paper_order/multi',
                    // import_url: 'exam/paper_order/import',
                    table: 'exam_paper_order',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);

                $("input[name='user_id']", form).addClass("selectpage").data("source", "user/user/index").data("field", "nickname");
                $("input[name='paper_id']", form).addClass("selectpage").data("source", "exam/paper/index").data("field", "title");

                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                // fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'user_id', title: __('User_id'), visible: false,},
                        {field: 'user.nickname', title: __('User.nickname'), operate: false},
                        {field: 'order_no', title: __('Order_no'), operate: 'LIKE'},
                        {field: 'paper_id', title: __('Paper_id'), visible: false,},
                        {field: 'paper.title', title: __('Paper.title'), operate: false},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'pay_money', title: __('Pay_money'), operate:'BETWEEN'},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                          field: 'expire_time', title: __('expire_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: function (value, row, index) {
                            if (value > 0) {
                              return Table.api.formatter.datetime(value, row, index);
                            } else {
                              return '永久有效';
                            }
                          }
                        },
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},

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
