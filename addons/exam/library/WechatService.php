<?php

namespace addons\exam\library;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support\Collection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * 微信公众号、小程序服务
 */
class WechatService
{
    /**
     * 微信配置信息
     *
     * @return array
     */
    private static function getConfig(): array
    {

        $wx_config = exam_getConfig('wx_config');
        switch (true) {
            case exam_is_empty_in_array($wx_config, 'appid'):
                exam_fail('缺少app_id配置');
            case exam_is_empty_in_array($wx_config, 'secret'):
                exam_fail('缺少secret配置');
            case exam_is_empty_in_array($wx_config, 'pay_mchid'):
                exam_fail('缺少mchid配置');
            case exam_is_empty_in_array($wx_config, 'pay_key'):
                exam_fail('缺少key配置');
            // case is_empty_in_array($pay_config, 'notify_url'):
            //     fail('缺少支付回调配置');
        }

        return [
            'app_id' => $wx_config['appid'],
            'secret' => $wx_config['secret'],
            'mch_id' => $wx_config['pay_mchid'],
            'key'    => $wx_config['pay_key'],
            // 'notify_url' => $pay_config['notify_url'],
        ];
    }

    /**
     * 获取支付回调地址
     *
     * @return string
     */
    private static function getPayNotifyUrl(): string
    {
        return request()->domain() . '/addons/exam/notify/pay';
    }

    /**
     * 公众号实例
     *
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public static function getApp(): \EasyWeChat\Payment\Application
    {
        return Factory::payment(self::getConfig() + ['notify_url' => self::getPayNotifyUrl()]);
    }

    /**
     * 小程序实例
     *
     * @return \EasyWeChat\MiniProgram\Application
     */
    public static function getMiniApp(): \EasyWeChat\MiniProgram\Application
    {
        return Factory::miniProgram(self::getConfig());
    }

    /**
     * 支付实例
     *
     * @return \EasyWeChat\Payment\Application
     */
    public static function getPayment(): \EasyWeChat\Payment\Application
    {
        $config = Factory::payment(self::getConfig());
        // $config['cert_path'] = APP_PATH . '/common/certs/apiclient_cert.pem';
        // $config['key_path']  = APP_PATH . '/common/certs/apiclient_key.pem';

        return $config;
    }

    /**
     * H5发起登录
     *
     * @param array $params 回调参数
     * @param int   $type   授权方式
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function login(array $params = [], int $type = 0)
    {
        $scopes = $type ? 'snsapi_userinfo' : 'snsapi_base';

        $callback_url = 'self::CALLBACK_URL';
        $callback_url = $params ? $callback_url . '?' . http_build_query($params) : $callback_url;

        $this->getApp()->oauth->withRedirectUrl($callback_url)->scopes([$scopes])->redirect()->send();
    }

    /**
     * 小程序登录
     *
     * @param string $code 前端js code
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function miniLogin(string $code)
    {
        $app = self::getMiniApp();
        return $app->auth->session($code);
    }

    /**
     * 统一支付
     *
     * @param string $openid       用户open id
     * @param string $out_trade_no 订单编号
     * @param int    $fee          订单金额
     * @param string $body         订单说明
     * @param string $attach
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws GuzzleException
     */
    public function unifyPay(string $openid, string $out_trade_no, int $fee, string $body, string $attach)
    {
        $payment = self::getPayment();

        $result = $payment->order->unify([
            'body'         => $body,
            'out_trade_no' => $out_trade_no,
            'total_fee'    => $fee,
            'trade_type'   => 'JSAPI',
            'openid'       => $openid,
            'attach'       => $attach,
            'notify_url'   => self::getPayNotifyUrl(),
        ]);

        if ($result['return_code'] == 'FAIL') {
            exam_fail('发起支付失败：' . $result['return_msg']);
        }
        if (empty($result['prepay_id'])) {
            exam_fail('发起支付失败：' . $result['err_code_des'] ?? '请使用微信登录的账号发起支付');
        }

        return $payment->jssdk->bridgeConfig($result['prepay_id'], false);
    }

    /**
     * 微信小程序消息解密
     * 比如获取电话等功能，信息是加密的，需要解密
     *
     * @param $session
     * @param $iv
     * @param $encryptedData
     * @return mixed
     */
    public function decryptedData($session, $iv, $encryptedData)
    {
        $app = self::getMiniApp();
        return $app->encryptor->decryptData($session, $iv, $encryptedData);
    }

    /**
     * 获取用户手机号
     *
     * @param $code
     * @return mixed
     */
    public function getMobile($code)
    {
        $app = self::getMiniApp();

        // 获取 access token 实例
        $accessToken = $app->access_token;
        $token       = $accessToken->getToken();

        $client   = new Client();
        $response = $client->post('https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=' . $token['access_token'], [
            'json' => [
                'code' => $code,
            ],
        ]);

        $result = $response->getBody()->getContents();
        $result = json_decode($result, true);
        if (!$result) {
            exam_fail('获取手机号失败');
        }

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            exam_fail('获取手机号失败：' . $result['errmsg'] ?? '未知错误');
        }

        return $result['phone_info']['purePhoneNumber'] ?? '';
    }
}
