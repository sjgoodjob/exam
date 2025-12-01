define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/cert/index' + location.search,
                    add_url: 'exam/cert/add',
                    edit_url: 'exam/cert/edit',
                    del_url: 'exam/cert/del',
                    multi_url: 'exam/cert/multi',
                    import_url: 'exam/cert/import',
                    table: 'exam_cert',
                }
            });

            var table = $("#table");

            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);

                $("input[name='cert_config_id']", form).addClass("selectpage").data("source", "exam/cert_config/index").data("params", {"custom[status]": "1"});
                $("input[name='cert_template_id']", form).addClass("selectpage").data("source", "exam/cert_template/index").data("params", {"custom[status]": "1"});
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
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'cert_config_id', title: __('Cert_config_id'), visible: false},
                        {field: 'config.name', title: __('Config.name'), operate: false},
                        {field: 'cert_template_id', title: __('Cert_template_id'), visible: false},
                        {field: 'template.name', title: __('Template.name'), operate: false},
                        {field: 'user_id', title: __('User_id'), visible: false},
                        {field: 'user.nickname', title: __('User.nickname'), operate: false},
                        {field: 'paper_id', title: __('Paper_id'), visible: false},
                        {field: 'paper.title', title: __('Paper.title'), operate: false},
                        {field: 'score', title: __('Score')},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'source', title: __('Source'), searchList: {"paper":__('Source paper'),"room":__('Source room'),"manual":__('Source manual')}, formatter: Table.api.formatter.normal},
                        // {field: 'expire_time', title: __('Expire_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},

                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            // 绑定批量添加按钮事件
            $('#btn-add2').on('click', function () {
                // Fast.api.open('exam/cert/add2', '考场用户批量发放证书', {
                //     callback: function () {
                //         table.bootstrapTable('refresh');
                //     }
                // });
                Fast.api.open('exam/cert/add2', '考场用户批量发放证书', {});
            });
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
                url: 'exam/cert/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
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
                                    url: 'exam/cert/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'exam/cert/destroy',
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
            Controller.api.bindCertConfigEvent();
        },
        add2: function () {
            Controller.api.bindevent();
            Controller.api.bindCertConfigEvent();
            Controller.api.bindRoomSignupEvent();

            // $("#c-room_grade_ids").data("format-item", function (row){
            //     console.log('format-item', row)
            //     let school = row.school ? row.school.name : '';
            //     let real_name = row.signup ? row.signup.real_name : '';
            //     let class_name = row.signup ? row.signup.class_name : '';
            //     return school + ' - ' + class_name + ' - ' + real_name;
            // });
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            bindCertConfigEvent: function (form) {
                $('#c-cert_config_id').on('change', function () {
                    $("#c-cert_template_id").selectPageClear()
                })
                $("#c-cert_template_id").data("params", function () {
                    const cert_config_id = $("input[name='row[cert_config_id]']").val();
                    return {
                        custom: {
                            cert_config_id: cert_config_id
                        }
                    }
                })
            },
            bindRoomSignupEvent: function (form) {
                $('#c-room_id').on('change', function () {
                    $("#c-room_grade_ids").selectPageClear()
                })
                $("#c-room_grade_ids").data("params", function () {
                    const room_id = $("input[name='row[room_id]']").val();
                    return {
                        custom: {
                            room_id: room_id
                        }
                    }
                });
            },
        }
    };
    return Controller;
});
