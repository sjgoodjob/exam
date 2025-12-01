define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/room/index' + location.search,
                    add_url: 'exam/room/add',
                    edit_url: 'exam/room/edit',
                    del_url: 'exam/room/del',
                    multi_url: 'exam/room/multi',
                    import_url: 'exam/room/import',
                    table: 'exam_room',
                }
            });

            var table = $("#table");

            // 在普通搜索渲染后
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);

                $("input[name='cate_id']", form).addClass("selectpage").data("source", "exam/cate/selectpage").data("params", {"custom[kind]": "ROOM"}).data("orderBy", "sort desc");
                $("input[name='paper_id']", form).addClass("selectpage").data("source", "exam/paper/index").data("field", "title").data("orderBy", "id desc");


                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });

            table.on('post-body.bs.table', function () {
                $(".btn-editone").data("area", ["50%", "80%"]);
                $("a.btn-add").attr('data-area', '["50%","80%"]');
                $("a.btn-edit").attr('data-area', '["50%","80%"]');
                $("a.btn-signup").attr('data-area', '["80%","80%"]');
                $("a.btn-grade").attr('data-area', '["80%","80%"]');
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                dblClickToEdit: false, // 是否启用双击编辑
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {
                            field: 'contents', title: __('Contents'), operate: false, formatter: Table.api.formatter.content
                            // formatter: function (value) {
                            //     return value.length > 20 ? value.substr(0, 20) + '...' : value;
                            // }
                        },
                        {field: 'cate_id', title: __('Cate_id'), visible: false},
                        {field: 'paper_id', title: __('Paper_id'), visible: false},
                        {field: 'cate.name', title: __('Cate.name'), operate: 'LIKE'},
                        {field: 'paper.title', title: __('Paper.title'), operate: 'LIKE'},
                        {
                            field: 'people_count', title: __('People_count'), formatter: function (value) {
                                return value > 0 ? value : '不限制';
                            }
                        },
                        {field: 'start_time', title: __('Start_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'end_time', title: __('End_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'status', title: __('Status'), searchList: {"NORMAL":__('Normal'),"HIDDEN":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'signup_mode', title: __('Signup_mode'), searchList: {"NORMAL":__('Signup_mode normal'),"PASSWORD":__('Signup_mode password'),"AUDIT":__('Signup_mode audit')}, formatter: Table.api.formatter.normal},
                        {
                            field: 'password', title: __('Password'), operate: false, formatter: function (value) {
                                return value ? value : '未设置';
                            }
                        },
                        {field: 'is_makeup', title: __('Is_makeup'), searchList: {"0":__('Is_makeup 0'),"1":__('Is_makeup 1')}, formatter: Table.api.formatter.normal},
                        {
                            field: 'makeup_count', title: __('Makeup_count'), formatter: function (value) {
                                return value > 0 ? value : '不补考';
                            }
                        },
                        // {field: 'is_rank', title: '排名', searchList: {"0":'未排名',"1":'已排名'}, formatter: Table.api.formatter.normal},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: function (value, row, index) {
                                var that = $.extend({}, this);
                                var table = $(this.table).clone(true);

                                // 过期隐藏编辑、删除按钮
                                if (row.end_time < Controller.api.getTimestamp()) {
                                    $(table).data('operate-edit', null);
                                    $(table).data('operate-del', null);
                                }
                                // 开启状态、已有报考隐藏删除按钮
                                if (row.status == 1 || row.signup_count > 0) {
                                    $(table).data('operate-del', null);
                                }

                                that.table = table;
                                return Table.api.formatter.operate.call(that, value, row, index);
                            },// Table.api.formatter.operate,
                            buttons: [

                                {
                                    name: 'signup',
                                    text: '报名列表',
                                    title: '报名列表',
                                    icon: 'fa fa-list',
                                    classname: 'btn btn-xs btn-info btn-dialog btn-signup',
                                    url: function (row) {
                                        return 'exam/room_signup/index?room_id=' + row.id
                                    },
                                },

                                {
                                    name: 'grade',
                                    text: '成绩列表',
                                    title: '成绩列表',
                                    icon: 'fa fa-list',
                                    classname: 'btn btn-xs btn-warning btn-dialog btn-grade',
                                    url: function (row) {
                                        return 'exam/room_grade/index?room_id=' + row.id
                                    },
                                },

                                // {
                                //     name: 'detail',
                                //     text: '详情',
                                //     title: '详情',
                                //     icon: 'fa fa-list',
                                //     classname: 'btn btn-xs btn-primary btn-dialog btn-detail',
                                //     url: 'room/detail',
                                // },

                                // {
                                //     name: 'rank',
                                //     text: '排行榜',
                                //     title: '排行榜',
                                //     icon: 'fa fa-flag',
                                //     classname: 'btn btn-xs btn-info btn-dialog',
                                //     url: 'room/rank',
                                //     // 按条件显示
                                //     visible: (row) => {
                                //         return row.is_rank > 0
                                //         // return row.end_time < Controller.api.getTimestamp()
                                //     }
                                // },
                            ],
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'exam/room/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'exam/room/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'exam/room/destroy',
                                    refresh: true
                                }
                            ],
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));

                Controller.api.bindSignupMode();
                Controller.api.bindMakeupMode();
            },

            // 绑定报名模式选择
            bindSignupMode: function () {
                $(document).on("change", "input[name='row[signup_mode]']", function(){
                    if ($(this).val() == 'PASSWORD') {
                        $('.password').show();
                        $('#c-password').attr('data-rule', "required");
                        $('form').validator("setField", "password", 'required');
                    } else {
                        $('.password').hide();
                        $('#c-password').removeAttr('data-rule');
                        $('form').validator("setField", "password", null);
                    }

                    console.log('signup_mode', $(this).val())
                });
                $("input[name='row[signup_mode]']:checked").trigger('change');
            },

            // 绑定补考模式选择
            bindMakeupMode: function () {
                $(document).on("change", "input[name='row[is_makeup]']", function(){
                    if ($(this).val() == 1) {
                        $('.makeup_count').show();
                        $('#c-makeup_count').attr('data-rule', "required");
                        $('form').validator("setField", "makeup_count", 'required');
                    } else {
                        $('.makeup_count').hide();
                        $('#c-makeup_count').removeAttr('data-rule');
                        $('form').validator("setField", "makeup_count", null);
                    }

                    console.log('is_makeup', $(this).val())
                });
                $("input[name='row[is_makeup]']:checked").trigger('change');
            },

            // 时间戳
            getTimestamp: function () {
                return (new Date()).getTime() / 1000;
            },
        }
    };
    return Controller;
});
