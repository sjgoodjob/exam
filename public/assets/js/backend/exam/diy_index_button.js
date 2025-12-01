define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/diy_index_button/index' + location.search,
                    add_url: 'exam/diy_index_button/add?page_style=' + page_style,
                    edit_url: 'exam/diy_index_button/edit',
                    del_url: 'exam/diy_index_button/del',
                    multi_url: 'exam/diy_index_button/multi',
                    import_url: 'exam/diy_index_button/import',
                    table: 'exam_diy_index_button',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                sortOrder: 'asc',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'page_style', title: __('page_style'), searchList: {"color":__('page_style color'),"color2":__('page_style color2'),"simple":__('page_style simple')}, formatter: Table.api.formatter.normal},
                        {field: 'type', title: __('Type'), searchList: {"icon":__('Type icon'),"image":__('Type image')}, formatter: Table.api.formatter.normal},
                        {field: 'icon', title: __('Icon'), operate: false},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'color', title: __('color'), operate: false},
                        {field: 'bg_color', title: __('bg_color'), operate: false},
                        {field: 'path', title: __('Path'), operate: 'LIKE'},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            // 绑定初始化数据按钮点击事件
            $(document).on("click", ".btn-initdata", function () {
                // Fast.api.ajax({
                //     url: 'exam/diy_index_button/initdata',
                //     loading: true,
                //     data: {page_style: page_style},
                //     callback: function (data) {
                //         Fast.api.success('初始化数据成功');
                //         table.bootstrapTable('refresh');
                //     }
                // });
                Fast.api.open('exam/diy_index_button/initdata?page_style=' + page_style, '初始化数据', {
                    callback: function (value) {
                        table.bootstrapTable('refresh');
                    }
                });
            });
        },
        add: function () {
            Controller.api.bindevent();
            // setTimeout(() => {
            //   if (page_style) {
            //     $("select[name='page_style']").val(page_style);
            //   }
            // }, 500)
        },
        edit: function () {
            Controller.api.bindevent();
        },
        initdata: function () {
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
