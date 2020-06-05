<?php

namespace Keqin\Dingtalk;

/**
 * 钉钉身份验证
 * @link https://ding-doc.dingtalk.com/doc#/serverapi2/vt6khw
 */
class Connect
{
    private $config;
    public $gateway = 'https://oapi.dingtalk.com';

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 根据 code 获取用户信息
     * @param string $code
     * @link https://ding-doc.dingtalk.com/doc#/serverapi2/kymkv6/M3fY1
     */
    public function getUserInfo($code)
    {
        $appKey = data_get($this->config, 'connect.appid');
        $appSecret = data_get($this->config, 'connect.appsecret');
        $time = $this->time();
        $signature = $this->sign($time, $appSecret);
        $url = "{$this->gateway}/sns/getuserinfo_bycode?accessKey=${appKey}&timestamp=${time}&signature=${signature}";
        $response = \Http::post($url, [
            'tmp_auth_code' => $code
        ]);
        return $response->json();
    }

    /**
     * 当前时间
     * @return time in ms
     */
    private function time(): int
    {
        return intval(microtime(true) * 1000);
    }

    /**
     * 钉钉签名
     * @param integer $time 当前时间戳，单位为 ms
     * @param string $appSecret 
     * @return string $signature 签名
     * @link https://ding-doc.dingtalk.com/doc#/faquestions/hxs5v9
     */
    private function sign($time, $appSecret): string
    {
        $signatureBin = hash_hmac('sha256', $time, $appSecret, true);
        $signature = urlencode(base64_encode($signatureBin));
        return $signature;
    }
}
