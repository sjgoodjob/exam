<?php

namespace app\admin\model\exam;

use think\Model;
use traits\model\SoftDelete;

class CertModel extends Model
{

    use SoftDelete;


    // 表名
    protected $name = 'exam_cert';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text',
        'source_text',
        'expire_time_text'
    ];


    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }

    public function getSourceList()
    {
        return ['paper' => __('Source paper'), 'manual' => __('Source manual')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSourceTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['source']) ? $data['source'] : '');
        $list  = $this->getSourceList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getExpireTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['expire_time']) ? $data['expire_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setExpireTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function config()
    {
        return $this->belongsTo(CertConfigModel::class, 'cert_config_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function template()
    {
        return $this->belongsTo(CertTemplateModel::class, 'cert_template_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function user()
    {
        return $this->belongsTo('app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
