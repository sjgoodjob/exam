define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/member_order/index' + location.search,
                    // add_url: 'exam/member_order/add',
                    // edit_url: 'exam/member_order/edit',
                    // del_url: 'exam/member_order/del',
                    // multi_url: 'exam/member_order/multi',
                    // import_url: 'exam/member_order/import',
                    table: 'exam_member_order',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);

                $("input[name='user_id']", form).addClass("selectpage").data("source", "user/user/index").data("field", "nickname");
                $("input[name='member_config_id']", form).addClass("selectpage").data("source", "exam/member_config/index").data("field", "name");

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
                        {field: 'user.avatar', title: __('User.avatar'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'order_no', title: __('Order_no'), operate: 'LIKE'},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'member_config_id', title: __('开通会员类型'), visible: false,},
                        {field: 'member_config.name', title: __('开通会员类型'), operate: false,},
                        // {field: 'type', title: __('Type'), searchList: {"VIP_MONTH":__('Type vip_month'),"VIP_YEAR":__('Type vip_year'),"VIP_LIFE":__('Type vip_life')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'pay_money', title: __('Pay_money'), operate:'BETWEEN'},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime')},
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
