<?php
namespace Keqin\Dingtalk;
use Illuminate\Http\Request;

class Robot
{
    use Timestamp;
    /**
     * 加签 Sign
     */
    private $secret;

    /**
     * Webhook Url
     */
    private $url;

    /**
     * Outgoing 机器人的 AppKey
     */
    public $appKey;
    /**
     * Outgoing 机器人的 AppSecret
     */
    private $appSecret;

    /**
     * 初始化
     * @param array $config 配置
     */
    function __construct(array $config = [])
    {
        $this->url = data_get($config, 'url');
        $this->secret = data_get($config, 'secret');
        $this->appSecret = data_get($config, 'appsecret');
        $this->appKey = data_get($config, 'appkey');
        if (!empty($secret)) {
            $this->secret = $secret;
        }
    }

    /**
     * 判断是否为钉钉提交的合法请求
     */
    public function isRequestLegal(Request $request): bool
    {
        $timestamp = $request->header('timestamp', 0);
        if ($this->hasExpired((int) $timestamp)) {
            return false;
        }
        $signInRequest = $request->header('sign');
        $sign = static::sign($timestamp, $this->appSecret);
        return $signInRequest === $sign;
    }

    /**
     * 判断请求是否已经过期
     * 
     * @param int $timestamp 请求的时间戳，单位 ms
     * @return boolen 是否已经过期
     * @link https://ding-doc.dingtalk.com/doc#/serverapi2/elzz1p
     */
    public function hasExpired(int $timestamp): bool
    {
        $oneHourAgo = $this->timestamp() - (3600 * 1000);
        if ($timestamp < $oneHourAgo) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * webhook 推送
     */
    public function post(array $body, array $options = []): array
    {
        \Arr::add($options, 'url', $this->url);
        \Arr::add($options, 'secret', $this->secret);
        $url = data_get($options, 'url');
        throw_if(empty($url), Exception::class, 'Webhook Url is Empty.');

        $secret = data_get($options, 'secret');
        if ($secret) {
            $timestamp = $this->timestamp();
            $sign = static::sign($timestamp, $secret);
            $url .= '&timestamp=' . $timestamp . '&sign=' . urlencode($sign);
        }

        $res = \Http::post($url, $body);
        return $res->json();
    }

    /**
     * 签名
     */
    public static function sign(string $timestamp, string $secret): string
    {
        $bin = hash_hmac("sha256", $timestamp."\n".$secret, $secret, true);
        return base64_encode($bin);
    }
}
