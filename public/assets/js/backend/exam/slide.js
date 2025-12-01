define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/slide/index' + location.search,
                    add_url: 'exam/slide/add',
                    edit_url: 'exam/slide/edit',
                    del_url: 'exam/slide/del',
                    multi_url: 'exam/slide/multi',
                    import_url: 'exam/slide/import',
                    table: 'exam_slide',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'status', title: __('Status'), searchList: {"NORMAL":__('Normal'),"HIDDEN":__('Hidden')}, formatter: Table.api.formatter.status},
                        // {field: 'front_info', title: __('Front_info'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
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
