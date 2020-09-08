<?php
namespace Keqin\Dingtalk;

use Illuminate\Support\Facades\Http;

class AccessToken
{
    public $appKey = '';
    private $appSecret = '';
    private $gateway = 'https://oapi.dingtalk.com';
    private $value = null;
    private $cacheKeyname;
    private $forceReturn = false;

    public function config($config)
    {
        $this->appKey = data_get($config, 'key');
        $this->appSecret = data_get($config, 'secret');
        $this->cacheKeyname = data_get($config, 'cache_keyname');
        return $this;
    }

    public function gateway($gateway)
    {
        $this->gateway = $gateway;
        return $this;
    }

    function __toString()
    {
        /**
         * 缓存中无 token 时，自动获取一遍
         */
        if (is_null($this->value)) {
            $token = \Cache::get($this->cacheKeyname);
            if (empty($token)) {
                $this->sync();
            } else {
                $this->value = $token;
            }
        }
        return $this->value;
    }

    public function fetch()
    {
        $url = $this->gateway.'/gettoken?appkey='.$this->appKey.'&appsecret='.$this->appSecret;
        $response = Http::get($url);
        $data = $response->json();
        $errcode = data_get($data, 'errcode');
        $errmsg = data_get($data, 'errmsg');
        $accessToken = data_get($data, 'access_token');
        if ($errcode && !$this->forceReturn) {
            throw new Exception($errmsg, $errcode);
        }
        return $data;
    }

    public function sync()
    {
        $token = $this->fetch();
        $accessToken = data_get($token, 'access_token');
        $expiresIn = data_get($token, 'expires_in');
        $this->value = $accessToken;
        \Cache::put($this->cacheKeyname, $accessToken, $expiresIn);
        return true;
    }
}
