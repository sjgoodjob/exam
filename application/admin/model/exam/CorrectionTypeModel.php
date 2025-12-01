<?php

namespace app\admin\model\exam;

use think\Model;


class CorrectionTypeModel extends Model
{

    

    

    // 表名
    protected $name = 'exam_correction_type';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
