define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    console.log('payable_types', payable_types)
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/pay_log/index' + location.search,
                    // add_url: 'exam/pay_log/add',
                    // edit_url: 'exam/pay_log/edit',
                    // del_url: 'exam/pay_log/del',
                    // multi_url: 'exam/pay_log/multi',
                    // import_url: 'exam/pay_log/import',
                    table: 'exam_pay_log',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);

                $("input[name='user_id']", form).addClass("selectpage").data("source", "user/user/index").data("field", "nickname");

                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });

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
                        {field: 'user_id', title: __('User_id'), visible: false,},
                        {field: 'user.nickname', title: __('User.nickname'), operate: false},
                        {field: 'user.avatar', title: __('User.avatar'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'out_trade_no', title: __('Out_trade_no'), operate: 'LIKE'},
                        {field: 'transaction_id', title: __('Transaction_id'), operate: 'LIKE'},
                        {field: 'pay_money', title: __('Pay_money'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'payable_id', title: __('Payable_id')},
                        {field: 'payable_type', title: __('Payable_type'), searchList: payable_types, formatter: Table.api.formatter.label},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime')},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        {
                            field: 'operate',
                            width: "150px",
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'detail',
                                    title: '查看详情',
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    extend: 'data-area=\'["80%", "80%"]\'',
                                    url: function (row) {
                                        let payInfo = Controller.api.payableTypeInfo(row.payable_type, row)
                                        return payInfo?.url
                                    },
                                },
                            ],
                            formatter: Table.api.formatter.operate
                        },
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
            },
            /**
             * 支付关联信息
             * @param value
             * @param row
             * @returns {null|{name: string, text: string, url: string}}
             */
            payableTypeInfo: function (value, row) {
                let types = [
                    {
                        name: 'MemberOrderModel',
                        text: '开通会员',
                        url: 'exam/member_order/index?id=' + row.payable_id,
                    },
                    {
                        name: 'PaperOrderModel',
                        text: '考试付费',
                        url: 'exam/paper_order/index?id=' + row.payable_id,
                    },
                    {
                        name: 'CateOrderModel',
                        text: '题库付费',
                        url: 'exam/cate_order/index?id=' + row.payable_id,
                    },
                ]

                for (let i = 0; i < types.length; i++) {
                    let type = types[i]
                    if (value.indexOf(type.name) > 0) {
                        console.log('type', type)
                        return type
                    }
                }

                return null
            }
        },
        formatter: {
            /**
             * 格式化支付关联类型
             * @param value
             * @param row
             * @returns {string}
             */
            payableType: function (value, row) {
                console.log('formatter payableType', value, row)
                let payInfo = Controller.api.payableTypeInfo(value, row)
                console.log('payInfo', payInfo)
                return '<label>' + payInfo?.text + '</label>'
            }
        }
    };
    return Controller;
});
