let configs_count = {}
let config_dialog = 0

define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'exam/paper/index' + location.search,
                    add_url: 'exam/paper/add',
                    edit_url: 'exam/paper/edit',
                    del_url: 'exam/paper/del',
                    multi_url: 'exam/paper/multi',
                    import_url: 'exam/paper/import',
                    table: 'exam_paper',
                }
            });

            var table = $("#table");
            //在普通搜索渲染后
            table.on('post-common-search.bs.table', function (event, table) {
                let form = $("form", table.$commonsearch);
                // , "isTree": true,
                $("input[name='cate_id']", form).addClass("selectpage").data("source", "exam/cate/index").data("params", {"custom[kind]": "PAPER"}).data("orderBy", "sort desc");
                $("input[name='subject_id']", form).addClass("selectpage").data("source", "exam/subject/index").data("params", {"isTree": "true"}).data("orderBy", "weigh desc");
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
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'subject_id', title: __('Subject_id'), autocomplete: false, visible: false},
                        {field: 'subject.name', title: __('Subject_id'), operate: false},
                        {field: 'cate_id', title: __('Cate_id'), autocomplete: false, visible: false},
                        {field: 'cate.name', title: __('Cate_id'), operate: false},
                        {field: 'cover_image', title: __('Cover_image'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'title', title: __('Title'), autocomplete: false, operate: 'LIKE'},
                        // {field: 'configs', title: __('Configs'), operate: 'LIKE'},
                        {field: 'quantity', title: __('Quantity'), operate: false},
                        {field: 'total_score', title: __('Total_score'), operate: false},
                        {field: 'pass_score', title: __('Pass_score'), operate: false},
                        {
                            field: 'mode',
                            title: __('Mode'),
                            searchList: {"RANDOM": __('Random'), "FIX": __('Fix')},
                            formatter: Table.api.formatter.normal
                        },
                        {
                            field: 'limit_time',
                            title: __('Limit_time'),
                            operate: false,
                            autocomplete: false,
                            formatter: function (value) {
                                return Controller.api.formatDuring(value)
                            }
                        },
                        {
                            field: 'start_time',
                            title: __('Start_time'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false,
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'end_time',
                            title: __('End_time'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false,
                            formatter: Table.api.formatter.datetime
                        },
                        {field: 'is_only_room', title: __('Is_only_room'), searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        {
                            field: 'uses',
                            title: __('uses'),
                            searchList: {"ONLY_PAY": __('ONLY_PAY'),"ALL": __('ALL'),"ONLY_MEMBER": __('ONLY_MEMBER'), },
                            formatter: Table.api.formatter.status
                        },
                        {field: 'price', title: __('price'), operate: false, formatter: Controller.api.formatPrice},
                        {field: 'member_price', title: __('member_price'), operate: false, formatter: Controller.api.formatPrice},
                        {
                          field: 'pay_effect_days', title: __('pay_effect_days'), operate: false, formatter: function (value) {
                            if (value) {
                              return value + '天';
                            }
                            return '无限制';
                          }
                        },
                        {
                            field: 'status',
                            title: __('Status'),
                            searchList: {"NORMAL": __('Normal'), "HIDDEN": __('Hidden')},
                            formatter: Table.api.formatter.status
                        },
                        {
                            field: 'createtime',
                            title: __('Createtime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false,
                            formatter: Table.api.formatter.datetime
                        },
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},

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
                url: 'exam/paper/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left'},
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
                                    url: 'exam/paper/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'exam/paper/destroy',
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
            Controller.api.bindevent()
            Controller.api.bindConfigs()
            Controller.api.bindTime()
            Controller.api.bindUses()
        },
        edit: function () {
            Controller.api.bindevent()
            Controller.api.bindConfigs()
            Controller.api.getCountScore()
            Controller.api.renderCountScore()
            Controller.api.bindTime()
            Controller.api.bindUses()
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"), function () {
                }, function () {
                }, function () {
                    let mode = $('input[name="row[mode]"]:checked').val()
                    console.log('submit mode', mode)
                    if (mode === 'FIX') {
                        $('#valid').click()
                        return valid_result;
                    } else {
                        let configs = $('#c-configs').val()
                        console.log('configs', configs)
                        if (!configs) {
                            Layer.alert('请配置试卷出题规则')
                            return false
                        }

                        let quantity = $('.span_quantity').text()
                        if (quantity !== $('#c-quantity').val()) {
                            Layer.alert('试卷出题规则的选取题数与试卷题目数量不一致')
                            return false
                        }

                        let total_score = $('.span_total_score').text()
                        if (total_score !== $('#c-total_score').val()) {
                            Layer.alert('试卷出题规则的总分与试卷总分不一致')
                            return false
                        }

                        let limit_time_hour = $('#c-limit_time_hour').val()
                        let limit_time_minute = $('#c-limit_time_minute').val()
                        let limit_time = (limit_time_hour ? limit_time_hour : 0) * 3600 + (limit_time_minute ? limit_time_minute : 0) * 60
                        $('#c-limit_time').val(limit_time)
                    }

                    return true
                });
            },

            // 选题模式设置
            bindConfigs: function () {
                // 选题模式弹窗
                $('.btn-configs').click(function () {
                    let quantity = $('#c-quantity').val()
                    let total_score = $('#c-total_score').val()

                    if (!quantity || !total_score) {
                        Layer.msg('请先设置题目数量和试卷总分')
                        return false
                    }

                    config_dialog = Layer.open({
                        type: 1,
                        shade: false,
                        title: '随机选题配置',
                        area: ['600px', '600px'],
                        zIndex: 1,
                        content: $('#configsDialog'),
                        cancel: function () {

                        }
                    })
                })

                // 选题库获取题数
                $(document).on("change", "#config-cate_id", function () {
                    Controller.api.getCountScore()
                })

                // 选取题更改
                $('.input_count').change(function () {
                    let type = $(this).data('type')
                    let count = $(this).val()
                    let score = $('.input_' + type + '_score').val()

                    Controller.api.calcCountScore(type, count, score)
                })

                // 每题分数更改
                $('.input_score').change(function () {
                    let type = $(this).data('type')
                    let count = Controller.api.formatVal($('.input_' + type).val())
                    let score = Controller.api.formatVal($(this).val())

                    Controller.api.calcCountScore(type, count, score)
                })

                // 分难度更改
                $('.checkbox_difficulty').change(function () {
                    let type = $(this).data('type')
                    let value = $(this).is(':checked')
                    let ul = $('.ul_' + type + '_difficulty')
                    let input_count = Controller.api.formatVal($('.input_' + type))
                    let input_score = Controller.api.formatVal($('.input_' + type + '_score'))
                    let span_score = Controller.api.formatVal($('.span_' + type + '_score'))

                    if (value) {
                        ul.removeClass('hide').show()
                        input_count.attr('disabled', 'disabled')
                        input_score.attr('disabled', 'disabled')
                        span_score.hide()

                        // 触发计算
                        $(ul.find('.input_count')[0]).trigger('change')
                    } else {
                        ul.addClass('hide').hide()
                        input_count.removeAttr('disabled', 'disabled')
                        input_score.removeAttr('disabled', 'disabled')
                        span_score.show()
                    }

                    // $('.input_count').trigger('change')
                })

                // 保存选题配置 TODO 弃用
                $('#btnSaveConfig').click(function () {
                    Controller.api.saveConfig()
                })
            },

            // 保存选题配置
            saveConfig() {
                let configs = {
                    cate_ids: $('#config-cate_id').val(),
                    all: {},
                    judge: {
                        difficulty: {}
                    },
                    single: {
                        difficulty: {}
                    },
                    multi: {
                        difficulty: {}
                    },
                    fill: {
                        difficulty: {}
                    },
                    short: {
                        difficulty: {}
                    },
                    material: {
                        difficulty: {}
                    },
                }

                // 总
                $('#divAll').find('span').each(function (index, ele) {
                    let key = $(ele).attr('class').replace('span_', '')
                    configs.all[key] = parseInt(Controller.api.formatVal($(ele).text()))
                })

                // 题型配置
                $('.input_kind_count').each(function (index, ele) {
                    let type = $(ele).data('type')

                    configs[type]['count'] = parseInt(Controller.api.formatVal($(ele).val()))
                    configs[type]['score'] = parseInt(Controller.api.formatVal($(ele).next('span').find('.input_score').val()))
                    configs[type]['total_score'] = configs[type]['count'] * configs[type]['score']
                    configs[type]['use_difficulty'] = $(ele).parent().find('.checkbox_difficulty').is(':checked')
                })

                // 难度配置
                $('.ul_difficulty').find('.input_count').each(function (index, ele) {
                    let type_key = $(ele).data('type').split('_')
                    let type = type_key[0]
                    let key = type_key[1]

                    configs[type].difficulty[key] = {
                        count: parseInt(Controller.api.formatVal($(ele).val())),
                        score: parseInt(Controller.api.formatVal($(ele).next('.input_score').val()))
                    }

                    configs[type].difficulty[key]['total_score'] = configs[type].difficulty[key].count * configs[type].difficulty[key].score
                })

                console.log('configs', configs)
                $('#c-configs').val(JSON.stringify(configs))
                Layer.close(config_dialog)
            },

            // 根据题库设置题数、分数
            getCountScore() {
                let cate_ids = $('#config-cate_id').val()

                if (cate_ids) {
                    configs_count = {}

                    Fast.api.ajax({
                        url: 'exam/question/getCount',
                        type: 'post',
                        data: {cate_ids: cate_ids}
                    }, function (data, ret) {
                        configs_count = data

                        if (!configs_count) {
                            return false
                        }

                        let quantity = $('#c-quantity').val()
                        let total_score = $('#c-total_score').val()
                        let sing_score = parseInt(total_score / quantity)

                        for (let key of Object.keys(configs_count)) {
                            let value = configs_count[key]

                            $('.span_' + key + '_total').text(value)
                            $('.input_' + key).attr('max', value)
                            $('.input_' + key + '_score').attr('max', sing_score)//.val(sing_score)
                        }

                        return false
                    });
                }
            },

            calcCountScore(type, count, score) {
                // 计算题型总分
                $('.span_' + type + '_total_score').text(count * score)

                // 计算分难度的总分
                if (type.indexOf('_') > 0) {
                    let parent_type = type.split('_')[0]
                    let ul = $('.ul_' + parent_type + '_difficulty')
                    let parent_count_input = $('.input_' + parent_type)
                    let parent_score_span = $('.span_' + parent_type + '_total_score')
                    let sum_count = 0
                    let sum_score = 0

                    ul.find('.input_count').each(function (index, ele) {
                        let single_score = $(ele).next('input').val()

                        sum_count += parseInt($(ele).val())
                        sum_score += parseInt($(ele).val()) * single_score
                    })

                    parent_count_input.val(sum_count)
                    parent_score_span.text(sum_score)
                }

                // 总题数
                let kind_total_count = 0
                $('.input_kind_count').each(function (index, ele) {
                    kind_total_count += parseInt($(ele).val())
                })
                $('.span_quantity').text(kind_total_count)

                // 计算总分
                let kind_total_score = 0
                $('.span_kind_total_score').each(function (index, ele) {
                    kind_total_score += parseInt($(ele).text())
                })
                $('.span_total_score').text(kind_total_score)

                // 保存配置
                Controller.api.saveConfig()
            },

            // 根据配置设置题数渲染数据
            renderCountScore() {
                // 考试时间渲染
                const limit_time = $('#c-limit_time').val() ? $('#c-limit_time').val() : 0
                if (limit_time) {
                    let hour = Math.floor(limit_time / 3600)
                    let minute = Math.floor(limit_time / 60) % 60

                    $('#c-limit_time_hour').val(hour)
                    $('#c-limit_time_minute').val(minute)
                }

                let configs_val = $('#c-configs').val()
                console.log('configs_val', configs_val)
                const config_json = configs_val && typeof configs_val === 'string' ? JSON.parse(configs_val) : {}
                if (config_json && config_json.cate_ids) {
                    for (const key in config_json) {
                        if (key === 'all') {
                            $('.span_quantity').text(config_json.all.quantity)
                            $('.span_total_score').text(config_json.all.total_score)
                        } else {
                            const kind_config = config_json[key]

                            $('.input_count[data-type="' + key + '"]').val(kind_config['count'])
                            $('.input_score[data-type="' + key + '"]').val(kind_config['score'])
                            $('.span_' + key + '_total_score').text(kind_config['total_score'])

                            if (kind_config['use_difficulty'] === true) {
                                $('.checkbox_' + key).click()

                                const difficulty_config = kind_config['difficulty']
                                for (const k in difficulty_config) {
                                    let difficulty_count = difficulty_config[k].count ? difficulty_config[k].count : 0
                                    let difficulty_score = difficulty_config[k].score ? difficulty_config[k].score : 0
                                    let difficulty_total_score = difficulty_count * difficulty_score

                                    $('.input_' + key + '_' + k).val(difficulty_count)
                                    $('.input_' + key + '_' + k + '_score').val(difficulty_score)
                                    $('.span_' + key + '_' + k + '_total_score').text(difficulty_total_score)
                                }
                            }
                        }
                    }

                    // 触发计算
                    // $($('.ul_judge_difficulty').find('.input_count')[0]).trigger('change')
                }
            },

            // 限定时间事件，59分转小时
            bindTime() {
                $('#c-limit_time_minute').change(function (ele) {
                    let minute = $(this).val()
                    if (minute >= 60) {
                        $('#c-limit_time_minute').val(0)

                        let hour_ctrl = $('#c-limit_time_hour')
                        hour_ctrl.val(parseInt(hour_ctrl.val()) + 1)
                    }
                })
            },

            // 绑定可用群体选择事件
            bindUses() {
                Controller.api.showUsesControl()
                $('input[type="radio"][name="row[uses]"]').change(function () {
                    Controller.api.showUsesControl()
                })
            },

            // 根据可用群体显示价格控件
            showUsesControl() {
                let uses = $('input[type="radio"][name="row[uses]"]:checked').val()
                $('.uses').hide()
                if (uses == 'ALL') {
                    // 价格置零
                    $('.uses input').each(function () {
                        $(this).val(0)
                    })
                } else if (uses == 'ONLY_MEMBER') {
                    $('.uses-member').show()
                } else if (uses == 'ONLY_PAY') {
                    $('.uses').show()
                }
            },

            // 格式化费用
            formatPrice(val) {
                if (val > 0) {
                    return val + '元'
                }
                return '<span class="text-success">免费</span>'
            },

            // 绑定固定选题配置按钮事件
            bindFixButton() {
                $('.btn-fix-configs').click(function () {
                    Fast.api.open('exam/question/select', '选择试题', {
                        area: ['90%', '90%'],
                        callback: function (data) {
                            if (!data) {
                                return
                            }
                        }
                    })
                });
            },

            // 秒数转时分秒格式
            formatDuring (second) {
                var hours = parseInt((second % (60 * 60 * 24)) / (60 * 60));
                var minutes = parseInt((second % (60 * 60)) / (60));
                var seconds = (second % (60));
                return hours + "时 " + minutes + "分 " + seconds + "秒";
            },

            // 获取数字值
            formatVal (val) {
                return isNaN(val) ? 0 : val
            },
        }
    };
    return Controller;
});
