define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/room_signup/index' + location.search,
                    add_url: 'exam/room_signup/add',
                    edit_url: 'exam/room_signup/edit',
                    del_url: 'exam/room_signup/del',
                    multi_url: 'exam/room_signup/multi',
                    import_url: 'exam/room_signup/import',
                    table: 'exam_room_signup',
                }
            });

            var table = $("#table");

            // 在普通搜索渲染后
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);

                $("input[name='user_id']", form).addClass("selectpage").data("source", "user/user/index").data("field", "nickname").data("orderBy", "id desc");
                $("input[name='room_id']", form).addClass("selectpage").data("source", "exam/room/index").data("orderBy", "id desc");

                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {
                            checkbox: true,
                            formatter:function(value, row, index){
                                if (row.status != 0) {
                                    console.log('checkbox', value, row, index, row.status, )
                                    return {
                                        disabled : true,
                                    }
                                }
                            }
                        },
                        {field: 'id', title: __('Id')},
                        {field: 'room_id', title: __('Room_id'), visible: false},
                        {field: 'room.name', title: __('Room.name'), operate: false},
                        {field: 'user_id', title: __('User_id'), visible: false},
                        {field: 'user.nickname', title: __('报名人昵称'), operate: false},
                        {field: 'school_id', title: __('school_id'), visible: false},
                        {field: 'school.name', title: __('school_id'), operate: false},
                        {field: 'class_name', title: __('class_name'), operate: 'LIKE'},
                        {field: 'real_name', title: __('Real_name'), operate: 'LIKE'},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'message', title: __('Message'), operate: false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},

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
