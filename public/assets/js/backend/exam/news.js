define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/news/index' + location.search,
                    add_url: 'exam/news/add',
                    edit_url: 'exam/news/edit',
                    del_url: 'exam/news/del',
                    multi_url: 'exam/news/multi',
                    import_url: 'exam/news/import',
                    table: 'exam_news',
                }
            });

            var table = $("#table");

            //当内容渲染完成给编辑按钮添加`data-area`属性，点击列表编辑按钮时全屏
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-add").data("area", ["80%", "80%"]);
                $(".btn-editone").data("area", ["80%", "80%"]);
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'images', title: __('Images'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        // {field: 'cover_image', title: __('Cover_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        // {field: 'contents', title: __('Contents')},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'status', title: __('Status'), searchList: {"NORMAL":__('Normal'),"HIDDEN":__('Hidden')}, formatter: Table.api.formatter.status},
                        // {field: 'front_info', title: __('Front_info'), operate: 'LIKE'},
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

                $(document).on('click', '#btnFrontend', function () {
                    var frontend = $('#frontend').val();
                    Fast.api.open('exam/config_info/frontend', '前端跳转设置', {
                        area: ['80%', '80%'],
                        callback: function (data) {
                            console.log('callback data', data)
                            $('#c-front_info').val(JSON.stringify(data))
                            return false;
                        }
                    });
                });
            }
        }
    };
    return Controller;
});
