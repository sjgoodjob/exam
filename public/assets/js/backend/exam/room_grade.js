define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/room_grade/index' + location.search,
                    // add_url: 'exam/room_grade/add',
                    // edit_url: 'exam/room_grade/edit',
                    del_url: 'exam/room_grade/del',
                    multi_url: 'exam/room_grade/multi',
                    import_url: 'exam/room_grade/import',
                    table: 'exam_room_grade',
                }
            });

            var table = $("#table");

            // 在普通搜索渲染后
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);

                $("input[name='cate_id']", form).addClass("selectpage").data("source", "exam/cate/selectpage").data("params", {"custom[kind]": "ROOM"}).data("orderBy", "sort desc");
                $("input[name='user_id']", form).addClass("selectpage").data("source", "user/user/index").data("field", "nickname").data("orderBy", "id desc");
                $("input[name='room_id']", form).addClass("selectpage").data("source", "exam/room/index").data("orderBy", "id desc");
                $("input[name='school_id']", form).addClass("selectpage").data("source", "exam/school/index").data("orderBy", "id desc");
                $("input[name='paper_id']", form).addClass("selectpage").data("source", "exam/paper/index").data("field", "title").data("orderBy", "id desc");

                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });

            //当内容渲染完成给编辑按钮添加`data-area`属性，点击列表编辑按钮时全屏
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-detail").data("area", ["80%", "80%"]);
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
                        {field: 'user_id', title: __('User_id'), visible: false},
                        {field: 'user.nickname', title: __('交卷人昵称'), operate: 'LIKE'},
                        {field: 'signup1.real_name', title: __('报名人姓名'), operate: 'LIKE'},
                        {field: 'signup1.phone', title: __('报名人手机'), operate: 'LIKE'},
                        {field: 'school_id', title: __('学校'), visible: false},
                        {field: 'school.name', title: __('学校'), operate: false},
                        {field: 'class_name', title: __('班级'), operate: 'LIKE'},
                        {field: 'cate_id', title: __('Cate_id'), visible: false},
                        {field: 'cate.name', title: __('Cate.name'), operate: false},
                        {field: 'room_id', title: __('Room_id'), visible: false},
                        {field: 'room.name', title: __('Room.name'), operate: false},
                        {field: 'paper_id', title: __('Paper_id'), visible: false},
                        {field: 'paper.title', title: __('Paper.title'), operate: 'LIKE'},
                        {field: 'score', title: __('Score'), sortable: true},
                        {field: 'is_pass', title: __('Is_pass'), searchList: {"0":__('Is_pass 0'),"1":__('Is_pass 1')}, formatter: Table.api.formatter.normal},
                        {field: 'is_makeup', title: __('Is_makeup'), searchList: {"0":__('Is_makeup 0'),"1":__('Is_makeup 1')}, formatter: Table.api.formatter.normal},
                        // {field: 'is_pre', title: '是否进入考场考试', searchList: {"0":'否',"1":'是'}, formatter: Table.api.formatter.normal},
                        {field: 'total_score', title: __('Total_score'), operate: false},
                        {field: 'total_count', title: __('Total_count'), operate: false},
                        {field: 'right_count', title: __('Right_count'), operate: false},
                        {field: 'error_count', title: __('Error_count'), operate: false},
                        {
                            field: 'rank', title: __('Rank'), operate: false, sortable: true, formatter: function (value) {
                                return value > 0 ? value : '无排名';
                            }
                        },
                        {
                            field: 'grade_time', title: __('Grade_time'), operate:false, formatter: Controller.formatter.formatSecond},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        {
                            field: 'operate', title: __('Operate'), table: table,
                            events: Table.api.events.operate,
                            buttons: [{
                                name: 'detail',
                                text: __('Detail'),
                                icon: 'fa fa-list',
                                classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                url: 'exam/room_grade/detail'
                            }],
                            formatter: Table.api.formatter.operate
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
        detail: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        },
        formatter: {
            formatSecond: function(second) {
                if (second == 0) return '不限时'

                let result = parseInt(second)
                let h = Math.floor(result / 3600) < 10 ? '0' + Math.floor(result / 3600) : Math.floor(result / 3600);
                let m = Math.floor((result / 60 % 60)) < 10 ? '0' + Math.floor((result / 60 % 60)) : Math.floor((result / 60 % 60));
                let s = Math.floor((result % 60)) < 10 ? '0' + Math.floor((result % 60)) : Math.floor((result % 60));

                let res = '';
                if(h !== '00') res += `${h}时`;
                if(m !== '00') res += `${m}分`;
                res += `${s}秒`;
                return res;
            }
        }
    };
    return Controller;
});
