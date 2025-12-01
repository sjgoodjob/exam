define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/grade/index' + location.search,
                    // add_url: 'exam/grade/add',
                    // edit_url: 'exam/grade/edit',
                    del_url: 'exam/grade/del',
                    // multi_url: 'exam/grade/multi',
                    // import_url: 'exam/grade/import',
                    table: 'exam_grade',
                }
            });

            var table = $("#table");
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);

                $("input[name='cate_id']", form).addClass("selectpage").data("source", "exam/cate/selectpage").data("params", {"custom[kind]": "PAPER"}).data("orderBy", "sort desc");
                $("input[name='user_id']", form).addClass("selectpage").data("source", "user/user/index").data("field", "nickname");
                $("input[name='paper_id']", form).addClass("selectpage").data("source", "exam/paper/index").data("field", "title");

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
                        {field: 'cate_id', title: __('Cate_id'), visible: false},
                        {field: 'cate.name', title: __('Cate_id'), operate: false},
                        {field: 'user_id', title: __('User_id'), visible: false},
                        {field: 'user.nickname', title: __('交卷人昵称'), operate: 'LIKE'},
                        {field: 'user.mobile', title: __('交卷人手机'), operate: 'LIKE'},
                        {field: 'paper_id', title: __('Paper_id'), visible: false},
                        {field: 'paper.title', title: __('Paper.title'), operate: false},
                        {field: 'score', title: __('Score'), operate: false},
                        {
                            field: 'is_pass', title: __('Is_pass'), searchList: {"1": __('及格'), "0": __('不及格')},
                            formatter: Table.api.formatter.normal
                        },
                        {field: 'total_score', title: __('Total_score'), operate: 'BETWEEN',},
                        {field: 'total_count', title: __('Total_count'), operate: false},
                        {field: 'right_count', title: __('Right_count'), operate: false},
                        {field: 'error_count', title: __('Error_count'), operate: false},
                        {
                            field: 'grade_time',
                            title: __('Grade_time'),
                            operate: false,
                            formatter: Controller.formatter.formatSecond
                        },
                        {
                            field: 'createtime',
                            title: __('Createtime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false,
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate', title: __('Operate'), table: table,
                            events: Table.api.events.operate,
                            buttons: [{
                                name: 'detail',
                                text: __('Detail'),
                                icon: 'fa fa-list',
                                classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                url: 'exam/grade/detail'
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
            },
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
