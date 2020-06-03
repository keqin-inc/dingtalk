<?php

namespace Keqin\Dingtalk;

class DingtalkService
{
    private $config;
    public $gateway = 'https://oapi.dingtalk.com';
    private $forceReturn = false;
    private $accessToken = '';

    function __construct($config)
    {
        $this->config = $config;
        $accessTokenConfig = data_get($this->config, 'app');
        $this->accessToken($accessTokenConfig);
    }

    public function accessToken($config = null)
    {
        if (!is_null($config)) {
            $this->accessToken = new AccessToken;
            $this->accessToken->config($config);
        }
        return $this->accessToken;
    }

    public function connect()
    {
        static $connect;
        if (empty($connect)) {
            $connect = new Connect($this->config);
        }
        return $connect;
    }

    /**
     * 遇到错误强制返回，不会抛出异常
     */
    public function forceReturn()
    {
        $this->forceReturn = true;
        return $this;
    }

    public function buildUrl($url, $queryString = [])
    {
        if (!empty($this->accessToken)) {
            $queryString['access_token'] = strval($this->accessToken);
        }
        if (strpos($url, '?') === false) {
            $delimiter = '?';
        } else {
            $delimiter = '&';
        }
        $url .= $delimiter.http_build_query($queryString);
        if (substr($url, 0 , 1) !== '/') {
            $url = '/'.$url;
        }
        return $this->gateway.$url;
    }

    public function post($url, $body)
    {
        $url = $this->buildUrl($url);
        return \Http::post($url, $body);
    }

    public function get($url, $queryString = [])
    {
        $url = $this->buildUrl($url, $queryString);
        return \Http::get($url);
    }
}
