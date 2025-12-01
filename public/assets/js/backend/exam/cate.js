define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    let current_kind = 'all';

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/cate/index' + location.search,
                    add_url: 'exam/cate/add?kind=' + current_kind,
                    edit_url: 'exam/cate/edit',
                    del_url: 'exam/cate/del',
                    multi_url: 'exam/cate/multi',
                    import_url: 'exam/cate/import',
                    table: 'exam_cate',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                pagination: false,
                commonSearch: false,
                search: false,
                showExport: false,//隐藏导出
                showToggle: false,//隐藏浏览模式
                showColumns: false,//隐藏显示隐藏模式
                searchFormVisible: true,//默认显示搜索表单
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'subject.name', title: __('subject_id'), operate: false},
                        {
                            field: 'name', title: __('Name'), operate: false, align: 'left', formatter:function (value, row, index) {
                                return value.toString().replace(/(&|&amp;)nbsp;/g, '&nbsp;');
                            }
                        },
                        {
                            field: 'kind',
                            title: __('Kind'),
                            searchList: {"QUESTION": __('Question'), "ROOM": __('Room')},// , "PAPER": __('Paper'), "COURSE": __('Course')
                            formatter: Table.api.formatter.normal
                        },
                        {
                            field: 'uses',
                            title: __('uses'),
                            searchList: {"ONLY_MEMBER": __('ONLY_MEMBER'),"ALL": __('ALL'),},
                            formatter: Table.api.formatter.label,
                            // formatter: function (value, row, index) {
                            //     if (row.kind == 'QUESTION') {
                            //         return Table.api.formatter.label(value, row, index);
                            //     }
                            //     return '-';
                            // }
                        },
                        // {
                        //     field: 'is_free',
                        //     title: __('is_free'),
                        //     searchList: {"0": __('is_free 0'),"1": __('is_free 1'),},
                        //     formatter: Table.api.formatter.normal
                        // },
                        // {field: 'icon', title: __('Icon'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // {field: 'parent_id', title: __('Parent_id'), operate: 'LIKE'},
                        // {field: 'sort', title: __('Sort')},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            //绑定TAB事件
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                // var options = table.bootstrapTable(tableOptions);
                var typeStr = $(this).attr("href").replace('#', '');
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    params.kind = typeStr;
                    return params;
                };
                table.bootstrapTable('refresh', {});
                current_kind = typeStr;
                if (typeStr == 'all') {
                    $('.btn-add').addClass('hide')
                } else {
                    $('.btn-add').removeClass('hide')
                }

                // 重新初始化表格参数
                Table.api.init({
                    extend: {
                        index_url: 'exam/cate/index' + location.search,
                        add_url: 'exam/cate/add?kind=' + current_kind,
                        edit_url: 'exam/cate/edit?kind=' + current_kind,
                        del_url: 'exam/cate/del',
                        multi_url: 'exam/cate/multi',
                        import_url: 'exam/cate/import',
                        table: 'cate',
                    }
                });

                return false;
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
                url: 'exam/cate/recyclebin' + location.search,
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
                                    url: 'exam/cate/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'exam/cate/destroy',
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

            setTimeout(function () {
                $("#c-kind").trigger("change");
            }, 100);
        },
        edit: function () {
            Controller.api.bindevent();
            $("#c-kind").trigger('change')
        },
        api: {
            bindevent: function () {
                $(document).on("change", "#c-kind", function () {
                    $("#c-parent_id option[data-type='all']").prop("selected", true);
                    $("#c-parent_id option").removeClass("hide");

                    // 隐藏非当前kind的数据
                    $("#c-parent_id option[data-type!='" + $(this).val() + "']").addClass("hide");
                    // 编辑时隐藏当前数据
                    let id = $('#id').val()
                    if (id) {
                        $("#c-parent_id option[value='" + id + "']").addClass("hide");
                    }
                    $("#c-parent_id option[value='0']").removeClass("hide");

                    // 刷新select项
                    $("#c-parent_id").data("selectpicker") && $("#c-parent_id").selectpicker("refresh");

                    // 可用群体
                    var kind = $(this).val();
                    if (kind === 'QUESTION' || kind === 'PAPER') {
                        $(".uses").removeClass("hide");
                    } else {
                        $(".uses").addClass("hide");
                    }
                });

                Form.api.bindevent($("form[role=form]"));

                $(document).on("change", "#c-parent_id", function () {
                    console.log('parent_id', $(this).val())
                });
            }
        }
    };
    return Controller;
});
