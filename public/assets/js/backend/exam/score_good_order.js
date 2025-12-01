define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/score_good_order/index' + location.search,
                    // add_url: 'exam/score_good_order/add',
                    // edit_url: 'exam/score_good_order/edit',
                    // del_url: 'exam/score_good_order/del',
                    // multi_url: 'exam/score_good_order/multi',
                    import_url: 'exam/score_good_order/import',
                    table: 'exam_score_good_order',
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
                        {field: 'order_no', title: __('Order_no'), operate: 'LIKE'},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'user_name', title: __('User_name'), operate: 'LIKE'},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'good_id', title: __('Good_id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'first_image', title: __('First_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'quantity', title: __('Quantity')},
                        // {field: 'price', title: __('Price')},
                        {field: 'amount', title: __('Amount')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"10":__('Status 10'),"20":__('Status 20'),"30":__('Status 30')}, formatter: Table.api.formatter.status},
                        {field: 'admin_remark', title: __('Admin_remark'), operate: false},
                        {field: 'ship_no', title: __('Ship_no'), operate: 'LIKE'},
                        {field: 'ship_remark', title: __('Ship_remark'), operate: false},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'ship_time', title: __('Ship_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'complete_time', title: __('Complete_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'good.name', title: __('Good.name'), operate: 'LIKE'},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            // halign: 'center',
                            align: 'left',
                            buttons: [
                                // {
                                //     name: 'detail',
                                //     text: '详情',
                                //     title: '详情',
                                //     icon: 'fa fa-list',
                                //     classname: 'btn btn-xs btn-primary btn-dialog btn-detail',
                                //     url: 'order/detail',
                                // },

                                {
                                    name: 'ship',
                                    text: '发货',
                                    title: '发货',
                                    icon: 'fa fa-truck',
                                    classname: 'btn btn-xs btn-info btn-dialog',
                                    url: 'exam/score_good_order/ship',
                                    callback: function (data) {
                                        $('.btn-refresh').click()
                                    },
                                    visible: (row) => {
                                        return row.status == 10
                                    }
                                },

                                {
                                    name: 'complete',
                                    text: '完成',
                                    title: '完成',
                                    icon: 'fa fa-check-circle',
                                    classname: 'btn btn-xs btn-success btn-ajax btn-complete',
                                    url: 'exam/score_good_order/complete',
                                    confirm: '确认完成该订单吗？',
                                    success: function (data, ret) {
                                        $('.btn-refresh').click()
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        $('.btn-refresh').click()
                                        Layer.alert(ret.msg);
                                        return false;
                                    },
                                    visible: (row) => {
                                        return row.status == 20
                                    }
                                },
                            ],
                        }
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
        ship: function () {
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
