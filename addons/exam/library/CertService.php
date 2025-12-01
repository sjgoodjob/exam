<?php
/**
 * Created by PhpStorm.
 * User         : zgcLives - 83746020
 * CreateTime   : 2023/12/23 12:04
 */

namespace addons\exam\library;

use addons\exam\enum\CertSource;
use app\admin\model\exam\CertConfigModel;
use app\admin\model\exam\CertModel;

/**
 * 证书服务
 */
class CertService
{
    public static function createCert($cert_config_id, $room_id, $paper_id, $user_id, $user_name, $score)
    {
        // 证书配置
        $cert_config = CertConfigModel::get($cert_config_id, ['templates']);
        if (!$cert_config || $cert_config['status'] != 1) {
            return false;
        }

        // 证书模板
        $templates = $cert_config['templates'];
        if (!$templates) {
            return false;
        }

        foreach ($templates as $template) {
            // 符合条件，生成证书
            if ($template['min_score'] > 0 && $template['min_score'] <= $score) {
                $field_config = is_string($template['field_config']) ? json_decode($template['field_config'], true) : $template['field_config'];

                $user_name_config = $field_config['user_name'];
                $score_config     = $field_config['score'];

                $user_name_config['text'] = $user_name;
                $score_config['text']     = $score;

                $data = [
                    'user_name' => $user_name_config,
                    'score'     => $score_config,
                    'image'     => $template['image'],
                    'name'      => $user_name . '_' . $score . '_' . date('YmdHi'),
                ];

                // 生成证书
                $image_url = self::generate($data);
                if ($image_url) {
                    // 保存证书记录
                    return CertModel::create([
                        'cert_config_id'   => $cert_config_id,
                        'cert_template_id' => $template['id'],
                        'name'             => $template['name'],
                        'user_id'          => $user_id,
                        'paper_id'         => $paper_id,
                        'room_id'          => $room_id,
                        'user_name'        => $user_name,
                        'score'            => $score,
                        'image'            => $image_url,
                        'source'           => $room_id ? CertSource::ROOM : CertSource::PAPER,
                        'status'           => 1,
                        'expire_time'      => 0,
                    ]);
                }

                break;
            }
        }

        return false;
    }

    /**
     * 生成证书
     *
     * @param $data
     * @return bool
     */
    public static function generate($data, $test = false)
    {
        $template_file = ROOT_PATH . "public/{$data['image']}";
        $font          = ROOT_PATH . 'public/assets/fonts/SourceHanSansK-Regular.ttf';
        // $font          = ROOT_PATH . 'public/assets/addons/exam/font/SourceHanSansCN-Regular.otf';
        // if ($test) {
        //     $font = ROOT_PATH . 'public/assets/fonts/SourceHanSansK-Regular.ttf';
        // }
        $export_root   = ROOT_PATH . 'public';
        $export_folder = $export_root . '/assets/addons/exam/certs';

        if (!file_exists($template_file)) {
            return false;
        }
        if (!is_dir($export_folder)) {
            mkdir($export_folder, 0755, true);
        }

        $export_url  = '/assets/addons/exam/certs/' . $data['name'] . '.jpg';
        $export_path = $export_root . $export_url;

        // 导入模板
        $image = imagecreatefromjpeg($template_file);
        if ($data['user_name']['status']) {
            $user_name_text      = $data['user_name']['text'];
            $user_name_size      = intval($data['user_name']['size'] ?? 16);
            $user_name_color_rgb = self::hex2rgb($data['user_name']['color'] ?? '#ffffff');
            $user_name_x         = $data['user_name']['x'] ?? 0;
            $user_name_y         = $data['user_name']['y'] ?? 0;
            // 设置姓名文字颜色
            $user_name_color = imagecolorallocate($image, $user_name_color_rgb[0], $user_name_color_rgb[1], $user_name_color_rgb[2]);
            // 姓名
            imagettftext($image, $user_name_size, 0, $user_name_x, $user_name_y, $user_name_color, $font, $user_name_text);
        }

        if ($data['score']['status']) {
            $score_text      = $data['score']['text'];
            $score_size      = intval($data['score']['size'] ?? 16);
            $score_color_rgb = self::hex2rgb($data['score']['color'] ?? '#ffffff');
            $score_x         = $data['score']['x'] ?? 0;
            $score_y         = $data['score']['y'] ?? 0;
            // 设置分数文字颜色
            $score_color = imagecolorallocate($image, $score_color_rgb[0], $score_color_rgb[1], $score_color_rgb[2]);
            // 分数
            imagettftext($image, $score_size, 0, $score_x, $score_y, $score_color, $font, $score_text);
        }

        // 保存图片
        imagejpeg($image, $export_path);

        return $export_url;
    }

    /**
     * 颜色转换
     *
     * @param $hex
     * @return array
     */
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return array($r, $g, $b);
    }
}
