define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/material_question/index' + location.search,
                    add_url: 'exam/material_question/add',
                    edit_url: 'exam/material_question/edit',
                    del_url: 'exam/material_question/del',
                    multi_url: 'exam/material_question/multi',
                    import_url: 'exam/material_question/import',
                    table: 'exam_material_question',
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
                        {field: 'parent_question_id', title: __('Parent_question_id')},
                        {field: 'question_id', title: __('Question_id')},
                        {field: 'createtime', title: __('Createtime')},
                        {field: 'updatetime', title: __('Updatetime')},
                        {field: 'question.title', title: __('Question.title'), operate: 'LIKE'},
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
