<?php

namespace app\admin\model\exam;

use addons\exam\model\BaseModel;
use traits\model\SoftDelete;


class RoomModel extends BaseModel
{
    use SoftDelete;

    // 表名
    protected $name = 'exam_room';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'start_time_text',
        'end_time_text',
        'status_text',
        'signup_mode_text',
        'is_makeup_text',
    ];

    /** 二维码保存目录 */
    const SAVE_DIR_PATH = 'uploads/qrcode';

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });

        // 同步保存分类的科目到考场
        self::afterWrite(function ($row) {
            if (!empty($row['cate_id'])) {
                $cate = CateModel::get($row['cate_id']);
                if ($cate && !empty($cate['subject_id'])) {
                    self::where('cate_id', $row['cate_id'])->update(['subject_id' => $cate['subject_id']]);
                }
            }
        });
    }


    public function getStatusList()
    {
        return ['NORMAL' => __('Normal'), 'HIDDEN' => __('Hidden')];
    }

    public function getSignupModeList()
    {
        return ['NORMAL' => __('Signup_mode normal'), 'PASSWORD' => __('Signup_mode password'), 'AUDIT' => __('Signup_mode audit')];
    }

    public function getIsMakeupList()
    {
        return ['0' => __('Is_makeup 0'), '1' => __('Is_makeup 1')];
    }

    public function getIsCreateQrcodeH5List()
    {
        return ['0' => '否', '1' => '是'];
    }


    public function getStartTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['start_time']) ? $data['start_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['end_time']) ? $data['end_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list  = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSignupModeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['signup_mode']) ? $data['signup_mode'] : '');
        $list  = $this->getSignupModeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsMakeupTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_makeup']) ? $data['is_makeup'] : '');
        $list  = $this->getIsMakeupList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setStartTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setEndTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function paper()
    {
        return $this->belongsTo(PaperModel::class, 'paper_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cate()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function cates()
    {
        return $this->belongsTo(CateModel::class, 'cate_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(SubjectModel::class, 'subject_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /*
     * 生成考场H5二维码图片
     */
    public static function createQrcodeImg($url, $id, $type = '')
    {
        $dir       = self::SAVE_DIR_PATH;
        $url_path  = '/' . $dir . '/' . "room_qrcode_{$type}_{$id}.png";
        $file_path = ROOT_PATH . DS . 'public' . DS . $url_path;

        // dd($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
        if (is_file($file_path)) {
            @unlink($file_path);
        }

        // 2.0.0之前的qrcode版本
        // $qrcode = new \Endroid\QrCode\QrCode($url);
        // $qrcode->setSize(300);
        // $qrcode->setMargin(2);
        // $qrcode->writeFile($file_path);

        // 2.0.0之后的qrcode版本
        $qrcode = \Endroid\QrCode\QrCode::create($url)
            ->setSize(300)
            ->setMargin(2);
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrcode);
        $result->saveToFile($file_path);

        return $url_path;
    }

    /**
     * 生成H5二维码
     *
     * @param $room_id
     * @return string
     */
    public static function createH5Qrcode($room_id)
    {
        $h5_url = ConfigInfoModel::getH5Url();
        $url    = $h5_url . 'pages/room/detail?id=' . $room_id;
        return RoomModel::createQrcodeImg($url, $room_id, 'h5');

    }
}
