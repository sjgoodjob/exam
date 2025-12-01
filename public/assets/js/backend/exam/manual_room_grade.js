define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/manual_room_grade/index' + location.search,
                    add_url: 'exam/manual_room_grade/add',
                    edit_url: 'exam/manual_room_grade/edit',
                    del_url: 'exam/manual_room_grade/del',
                    multi_url: 'exam/manual_room_grade/multi',
                    import_url: 'exam/manual_room_grade/import',
                    table: 'exam_manual_room_grade_log',
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
                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'paper_id', title: __('Paper_id')},
                        {field: 'room_id', title: __('Room_id')},
                        {field: 'grade_id', title: __('Grade_id')},
                        {field: 'question_id', title: __('Question_id')},
                        {field: 'before_score', title: __('Before_score')},
                        {field: 'after_score', title: __('After_score')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime')},
                        {field: 'updatetime', title: __('Updatetime')},
                        {field: 'admin.nickname', title: __('Admin.nickname'), operate: 'LIKE'},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        {field: 'user.mobile', title: __('User.mobile'), operate: 'LIKE'},
                        {field: 'paper.title', title: __('Paper.title'), operate: 'LIKE'},
                        {field: 'room.name', title: __('Room.name'), operate: 'LIKE'},
                        {field: 'question.title', title: __('Question.title'), operate: 'LIKE'},
                        {field: 'grade.score', title: __('Grade.score')},
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
