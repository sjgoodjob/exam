const option_name = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
var init_count = 0;
var fa_form;

define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form, Upload) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/question/index' + location.search,
                    add_url: 'exam/question/add',
                    edit_url: 'exam/question/edit',
                    del_url: 'exam/question/del',
                    multi_url: 'exam/question/multi',
                    import_url: 'exam/question/import',
                    ai_url: 'exam/question/ai',

                    table: 'exam_question',
                    
                }
            });

            var table = $("#table");
            //在普通搜索渲染后
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);
                $("input[name='cate_id']", form).addClass("selectpage").data("source", "exam/cate/selectpage").data("params", {"custom[kind]": "QUESTION","isTree":true}).data("orderBy", "sort desc");
                // $("input[name='is_material_child']", form).val(0);
                // $("input[name='exam_type_id']", form).addClass("selectpage").data("source", "exam_type/index").data("orderBy", "sort desc");

                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });

            //当内容渲染完成给编辑按钮添加`data-area`属性，点击列表编辑按钮时全屏
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-add").data("area", ["100%", "100%"]);
                $(".btn-editone").data("area", ["100%", "100%"]);
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                showExport: false,//隐藏导出
                showToggle: false,//隐藏浏览模式
                showColumns: false,//隐藏显示隐藏模式
                searchFormVisible: true,//默认显示搜索表单
                escape:false,//false解析html,默认为true不解析
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'cate_id', title: __('Cate_id'), autocomplete: false, visible: false},
                        {field: 'cate.name', title: __('Cate_id'), operate: false},
                        // {field: 'exam_type_id', title: __('Exam_type_id'), visible: false},
                        // {field: 'examtype.name', title: __('Exam_type_id'), operate: false},
                        {
                            field: 'kind',
                            title: __('Kind'),
                            searchList: {"JUDGE": __('Judge'), "SINGLE": __('Single'), "MULTI": __('Multi'), "FILL": __('Fill'), "SHORT": __('Short'), "MATERIAL": __('Material')},
                            formatter: Table.api.formatter.normal,
                            // operate: "IN",
                        },
                        {
                          field: 'title', title: __('Title'), autocomplete: false, operate: 'LIKE',
                          sortable: true,
                          // formatter: Table.api.formatter.content,
                          formatter: function (value, row, index) {
                            if (row.is_repeat) {
                              value = '<span class="text-danger">' + value + '</span>';
                            }
                            return Table.api.formatter.content.call(this, value, row, index);
                          },
                        },
                        {
                          field: 'is_material_child',
                          title: __('属于材料题子题'),
                          searchList: {"0": __('否'), "1": __('是')},
                          defaultValue: 0,
                          visible: false
                        },
                        {
                            field: 'difficulty',
                            title: __('Difficulty'),
                            searchList: {"EASY": __('Easy'), "GENERAL": __('General'), "HARD": __('Hard')},
                            formatter: Table.api.formatter.normal
                        },
                        {
                            field: 'answer', title: __('Answer'), halign: 'center', align: 'left', operate: false, formatter: function (value, row, index) {
                                var answer = value;
                                if (row.kind == 'FILL') {
                                    try {
                                        answer = '';
                                        let fill_answers = JSON.parse(row.answer);
                                        for (let i = 0; i < fill_answers.length; i++) {
                                            answer += '填空位' + (i + 1) + '：' + fill_answers[i].answers.join('、') + '<br>';
                                        }
                                    } catch (e) {
                                        console.log('answer', index, answer, e);
                                        return value;
                                    }
                                } else if (row.kind == 'SHORT') {
                                    try {
                                        let short_answers = JSON.parse(row.answer);
                                        answer = '标准答案：' + short_answers.answer + '<br>';
                                        for (let i = 0; i < short_answers.config.length; i++) {
                                            // answer += '关键词' + (i + 1) + '：' + short_answers[i].answer + '<br>';
                                            answer += '关键词' + (i + 1) + '：' + short_answers.config[i].answer + '(' + short_answers.config[i].score + '分)<br>';
                                        }
                                    } catch (e) {
                                        console.log('short answer', index, answer, e);
                                        return value;
                                    }
                                } else if (row.kind == 'MATERIAL') {
                                    return '-';
                                }

                                console.log('answer', value, answer);
                                return answer;
                            }
                        },
                        {
                            field: 'status',
                            title: __('Status'),
                            searchList: {"NORMAL": __('Normal'), "HIDDEN": __('Hidden')},
                            formatter: Table.api.formatter.status,
                            operate: false
                        },
                        /*{
                            field: 'createtime',
                            title: __('Createtime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false,
                            formatter: Table.api.formatter.datetime
                        },*/
                        /*{
                            field: 'updatetime',
                            title: __('Updatetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false,
                            formatter: Table.api.formatter.datetime
                        },*/

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

            // 打开导入页面
            $('.btn-importPage').click(function () {
                Fast.api.open('exam/question/import', '导入试题', {
                    area: ['800px', '380px'],
                    callback: function (data) {
                        table.bootstrapTable('refresh');
                    }
                });
            });
             $('#btn-import-ai').click(function () {
                Fast.api.open('exam/question/ai', 'AI出题', {
                    area: ['90%', '90%'],
                    callback: function (data) {
                        // 可选：比如出题完成刷新表格
                        $("#table").bootstrapTable('refresh');
                    }
                });
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
                url: 'exam/question/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                escape:false,//false解析html,默认为true不解析
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left', formatter: Table.api.formatter.content},
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
                                    url: 'exam/question/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'exam/question/destroy',
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
            fa_form = Form
            Controller.api.bindevent();
            $('input[name="row[kind]"]')[0].click()
            setTimeout(() => {
                optionCtrl(Form);
                init_count++;
            }, 500)
        },
         ai:function () {
            fa_form = Form

            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
            optionCtrl(Form, 'edit');
            init_count++;
        },
        image: function () {
            // Controller.api.bindevent();
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                let img = $('#c-imgs').val();
                if (!img) {
                    Toastr.warning('未上传图片文件');
                }

                Fast.api.close(img);
            }, function (data, ret) {
                Toastr.error("失败");
            });
        },
        import: function () {
            Controller.api.bindevent();

            // 手动绑定导入事件
            Upload.api.upload($('.btn-import'), function (data, ret) {
                let cate = $('#c-cate_id').val();
                // let exam_type = $('#c-exam_type_id').val();

                if (!cate) { //  || !exam_type
                    Fast.api.msg('请先选择所属类型及考试分类再进行上传！')
                    return false;
                }

                $('#file_url').val(data.url);
                Fast.api.ajax({
                    url: 'exam/question/importExcel',
                    data: {
                        file: data.url,
                        cate: cate,
                        // exam_type: exam_type
                    },
                }, function (data, ret) {
                    console.log(data, ret)
                    $('#question_count').html(`本次上传识别到 ${data.count} 道题`)
                });
            });

            $('#test').click(function () {
                Fast.api.ajax({
                    url: 'exam/question/test',
                }, function (data, ret) {
                    console.log(data, ret)
                    return false
                })
            })
        },
        // 选择题目页面
        select: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/question/select' + location.search,
                    // add_url: 'exam/question/add',
                    // edit_url: 'exam/question/edit',
                    // del_url: 'exam/question/del',
                    // multi_url: 'exam/question/multi',
                    // import_url: 'exam/question/import',
                    table: 'exam_question',
                }
            });

            var table = $("#table");
            //在普通搜索渲染后
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);
                $("input[name='cate_id']", form).addClass("selectpage").data("source", "exam/cate/selectpage").data("params", {"custom[kind]": "QUESTION"}).data("orderBy", "sort desc");
                // $("input[name='exam_type_id']", form).addClass("selectpage").data("source", "exam_type/index").data("orderBy", "sort desc");

                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                showExport: false,//隐藏导出
                showToggle: false,//隐藏浏览模式
                showColumns: false,//隐藏显示隐藏模式
                searchFormVisible: true,//默认显示搜索表单
                escape:false,//false解析html,默认为true不解析
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'cate_id', title: __('Cate_id'), autocomplete: false, visible: false},
                        {field: 'cate.name', title: __('Cate_id'), operate: false},
                        {
                            field: 'kind',
                            title: __('Kind'),
                            searchList: {"JUDGE": __('Judge'), "SINGLE": __('Single'), "MULTI": __('Multi'), "FILL": __('Fill'), "SHORT": __('Short'), "MATERIAL": __('Material')},
                            formatter: Table.api.formatter.normal
                        },
                        {field: 'title', title: __('Title'), autocomplete: false, operate: 'LIKE', formatter: Table.api.formatter.content},
                        {
                            field: 'difficulty',
                            title: __('Difficulty'),
                            searchList: {"EASY": __('Easy'), "GENERAL": __('General'), "HARD": __('Hard')},
                            formatter: Table.api.formatter.normal
                        },
                        {
                            field: 'answer', title: __('Answer'), halign: 'center', align: 'left', operate: false, formatter: function (value, row, index) {
                                var answer = value;
                                // 填空题
                                if (row.kind == 'FILL') {
                                    try {
                                        answer = '';
                                        let fill_answers = JSON.parse(row.answer);
                                        for (let i = 0; i < fill_answers.length; i++) {
                                            answer += '填空位' + (i + 1) + '：' + fill_answers[i].answers.join('、') + '<br>';
                                        }
                                    } catch (e) {
                                        console.log('fill answer', index, answer, e);
                                        return value;
                                    }
                                } else if (row.kind == 'SHORT') {
                                    try {
                                        answer = '';
                                        let short_answers = JSON.parse(row.answer);
                                        for (let i = 0; i < short_answers.length; i++) {
                                            // answer += '关键词' + (i + 1) + '：' + short_answers[i].answer + '<br>';
                                            answer += '关键词' + (i + 1) + '：' + short_answers[i].answer + '(' + short_answers[i].score + '分)<br>';
                                        }
                                    } catch (e) {
                                        console.log('short answer', index, answer, e);
                                        return value;
                                    }
                                }

                                return answer;
                            }
                        },
                        {
                            field: 'status',
                            title: __('Status'),
                            searchList: {"NORMAL": __('Normal'), "HIDDEN": __('Hidden')},
                            formatter: Table.api.formatter.status,
                            operate: false
                        },
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            // 批量确认选择
            $('.btn-confirm-choose').click(function () {
                // Table.api.select
                // var ids = Table.api.selectedids(table);//获取选中列的id
                // if (ids.length == 0){
                //     layer.alert("请选择题目");
                //     return false;
                // }

                var rows = $("#table").bootstrapTable('getSelections');
                console.log('select questions', rows, rows.length, 'length')
                if (rows.length == 0){
                    layer.alert("请选择题目");
                    return false;
                }

                Fast.api.close(rows);
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent(
                    $("form[role=form]"),

                    () => {
                        console.log('success', this)
                    },
                    () => {
                        console.log('fail', this)
                    },
                    () => {
                        console.log('submit', this, valid_result)
                        // 触发验证
                        $('#valid').click()
                        return valid_result;
                    },

                );

                // 题库和材料题联动
                $('#c-cate_id').on('change', function () {
                    $("#c-material_question_id").selectPageClear()
                });
                $("#c-material_question_id").data("params", function () {
                    const cate_id = $("input[name='row[cate_id]']").val();
                    return {
                        custom: {
                            cate_id: cate_id,
                            kind: 'MATERIAL',
                        }
                    }
                });
            }
        }
    };
    return Controller;
});

function optionCtrl(Form, type = 'add') {
    console.log('init_count', init_count)
    if (init_count > 0) {
        return;
    }

    $('.btn-append').html('<i class="fa fa-plus"></i> 添加选项');
    // 添加选项
    $(document).on("fa.event.appendfieldlist", '[data-name="row[options_json]"] .btn-append', function () {
        console.log('append xxx')
        $('.btn-dragsort').hide();
        let dd = $('dd[class="form-inline"]');
        let options_img = $('#c-options_img');
        let question_num = dd.length;
        if (question_num > 8) {
            Toastr.error('选项不能超过8个');
            // $('input[name="row[options_json]['+question_num+'][value]"]').next().click();
            // $('input[name="row[options_json]['+question_num+'][value]"]').parent().remove();
            dd[question_num - 1].remove();
            return false;
        }

        // 重排选项名称
        sortOptions();
        // 删除选项
        bindRemoveOption();

        // 添加上传图片控件
        // $(dd[question_num - 1]).append(imageUploadHtml('row[options_json][' + (question_num - 1) + '][image]'));
        // $(dd[question_num - 1]).append(editorHtml('row[options_json][' + (question_num - 1) + '][image]'));
        // Form.api.bindevent($("form[role=form]"));
        // 添加设为答案按钮
        $(dd[question_num - 1]).append('<span class="btn btn-sm btn-info btn-set m-l-5" data-type="SET">设为答案</span>');
        // 添加上传图片按钮
        $(dd[question_num - 1]).append('<span class="btn btn-sm btn-success btn-image m-l-5" data-type="UPLOAD" id="upload_' + (question_num - 1) + '" data-id="' + (question_num - 1) + '">上传图片</span>');

        if (options_img.val()) {
            let option_img_json = JSON.parse(options_img.val())

            if(option_img_json) {
                option_img_json.forEach((item) => {
                    let index = option_name.indexOf(item.key)
                    // 不知为啥会执行两次
                    if ($('.option-preview-' + index).length === 0) {
                        $(dd[index]).append(previewHtml(index, item.value));
                    }
                })
            }
        }

        bindSetAnswer();
        bindUploadImage();

        if (type === 'edit') {
            initEditBtn()
        }
    });
}

// 编辑时处理按钮文本
function initEditBtn() {
    let answer = $('#c-answer').val()
    if (!answer) return

    console.log('initEditBtn answer', answer)
    $('.btn-set').each(function (index, ele) {
        const key = $($(ele).parent().find('input')[0]).val()
        console.log('initEditBtn key', key)
        if (key && answer.indexOf(key) > -1) {
            $(ele).removeClass('btn-info').addClass('btn-warning').text('取消答案').data('type', 'CANCEL')
        }
    })
}

// 监听删除选项按钮事件
function bindRemoveOption() {
    $('.btn-remove').click(function () {
        //延迟重排选项名称
        setTimeout(() => {
            sortOptions();
        }, 300);
    });
}

// 监听设为答案按钮事件
function bindSetAnswer() {
    $('.btn-set').unbind('click').click(function () {
        let type = $(this).data('type');
        let name = $($(this).parent().children('input')[0]).val();
        let answer_type = getAnswerType();
        let c_answer = $('#c-answer');
        // 设为答案
        if (type === 'SET') {
            // 单选
            if (answer_type === 'SINGLE' || !c_answer.val()) {
                c_answer.val(name);

                $('.btn-set').removeClass('btn-warning').addClass('btn-info').text('设为答案').data('type', 'SET');
                $(this).removeClass('btn-info').addClass('btn-warning').text('取消答案').data('type', 'CANCEL');
            } else {// 多选
                let answers = c_answer.val();
                let answer_arr = answers.split(',');

                answer_arr.push(name);
                answer_arr = unique(answer_arr);
                c_answer.val(answer_arr.join(','));

                $(this).removeClass('btn-info').addClass('btn-warning').text('取消答案').data('type', 'CANCEL');
            }
        } else {// 取消答案
            // 单选
            if (answer_type === 'SINGLE') {
                if (c_answer.val() === name) {
                    c_answer.val('');
                    $(this).removeClass('btn-warning').addClass('btn-info').text('设为答案').data('type', 'SET');
                }
            } else {// 多选
                let answers = c_answer.val();
                let answer_arr = answers.split(',');
                answer_arr = answer_arr.filter(function (item) {
                    return item !== name;
                });
                console.log(answer_arr);
                c_answer.val(answer_arr.join(','));

                $(this).removeClass('btn-warning').addClass('btn-info').text('设为答案').data('type', 'SET');
            }
        }
    });
}

// 监听上传图片按钮事件
function bindUploadImage() {
    $('.btn-image').unbind('click').click(function () {
        let index = $(this).data('id')
        Fast.api.open('exam/question/image', '选项图片', {
            area: ['500px', '300px'],
            callback: function (data) {
                if (!data) {
                    return
                }
                // if ($($('dd[class="form-inline"]')[index]).find('.plupload-preview').length > 0) {
                //     return
                // }

                // 加预览图
                $($('dd[class="form-inline"]')[index]).append(previewHtml(index, data))
                // 赋值图片数据
                $('#c-options_img').html(JSON.stringify(getAllPreview()))

                bindDeleteImage()
            }
        })
    });

    bindDeleteImage()
}

// 删除图片按钮事件
function bindDeleteImage() {
    $('.btn-option-img-trash').unbind('click').click(function () {
        let index = $(this).data('id')
        $('#c-option_preview_' + index).remove()
        // 赋值图片数据
        $('#c-options_img').html(JSON.stringify(getAllPreview()))
    });
}

// 重排选项名称
function sortOptions() {
    $('dd[class="form-inline"]').each(function (index, ele) {
        console.log(option_name[index]);
        // 配置选项名并设为只读
        $($(this).find('.form-control')[0]).val(option_name[index]).attr('readonly', 'readonly');
    });
}

function unique(arr) {
    arr.sort();
    let newArr = [arr[0]];
    for (let i = 1; i < arr.length; i++) {
        if (arr[i] !== newArr[newArr.length - 1]) {
            newArr.push(arr[i]);
        }
    }
    return newArr;
}

function getAnswerType() {
    let kind = $('input[type="radio"][name="row[kind]"]:checked').val();
    return kind === 'MULTI' ? 'MULTI' : 'SINGLE';
}

function getAllPreview() {
    let options_img = []

    $('.option-preview').each(function (index, ele) {
        index = $(ele).data('id')
        options_img.push({
            key: getOptionNameByIndex(index),
            value: $(this).data('url')
        })
    })

    return options_img
}

function getOptionNameByIndex(index) {
    console.log('getOptionNameByIndex', index)
    return $($($('dd[class="form-inline"]')[index]).find('.form-control')[0]).val();
}

function previewHtml(id, value) {
    let cdnurl = Fast.api.cdnurl(value)
    return '<ul class="row list-inline plupload-preview option-preview-' + id + '" data-listidx="0" id="c-option_preview_' + id + '">' +
        '<li class="col-xs-3">' +
        '<a href="' + value + '" data-url="' + value + '" data-id="' + id + '" target="_blank" class="thumbnail option-preview">' +
        '<img src="' + cdnurl + '" class="img-responsive">' +
        '</a>' +
        '<a href="javascript:;" class="btn btn-danger btn-xs btn-trash btn-option-img-trash" data-id="' + id + '"><i class="fa fa-trash"></i></a>' +
        '</li>' +
        '</ul>';
}

function editorHtml(id, value) {
    return '<textarea id="' + id + '" class="form-control editor" name="' + id + '" value="' + value + '" ></textarea>';
}

function imageUploadHtml(id) {
    let html = '<div class="input-group" data-attr="image-upload">'
    html += '<input id="' + id + '" class="form-control" name="row[' + id + ']" size="300" type="text" >';
    html += '<div class="input-group-addon no-border no-padding">';
    html += '<span><button type="button" id="plupload-' + id + '" class="btn btn-danger plupload" data-input-id="' + id + '" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-' + id + '"><i class="fa fa-upload"></i> 上传</button></span>';
    html += '<span><button type="button" id="fachoose-image_url" class="btn btn-primary fachoose" data-input-id="' + id + '" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> 选择</button></span>';
    html += '</div>';
    html += '<span class="msg-box n-right" for="' + id + '"></span>';
    html += '</div>';
    html += '<ul class="row list-inline plupload-preview" id="p-' + id + '"></ul>';
    return html;
}

