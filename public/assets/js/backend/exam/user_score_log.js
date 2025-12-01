define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/user_score_log/index' + location.search,
                    add_url: 'exam/user_score_log/add',
                    edit_url: 'exam/user_score_log/edit',
                    del_url: 'exam/user_score_log/del',
                    multi_url: 'exam/user_score_log/multi',
                    import_url: 'exam/user_score_log/import',
                    table: 'exam_user_score_log',
                }
            });

            var table = $("#table");

            var table = $("#table");
            // table.on('post-common-search.bs.table', function (event, table) {
            //     let form = $("form", table.$commonsearch);
            //
            //     $("input[name='user_id']", form).addClass("selectpage").data("source", "user/user/index").data("field", "nickname");
            //
            //     Form.events.cxselect(form);
            //     Form.events.selectpage(form);
            // });

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
                        {field: 'user.avatar', title: __('User.avatar'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'score', title: __('Score')},
                        {field: 'before', title: __('Before')},
                        {field: 'after', title: __('After')},
                        {field: 'type', title: __('Type'), searchList: typeList, formatter: Table.api.formatter.status},
                        {field: 'memo', title: __('Memo'), operate: 'LIKE'},
                        // {field: 'date', title: __('Date')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
