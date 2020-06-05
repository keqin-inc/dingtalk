<?php
namespace Keqin\Dingtalk\Tests;

use Illuminate\Support\Facades\Http;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $config;

    // 当前的模拟 AccessToken
    protected $mockAccessToken = 'mockaccesstoken123';
    protected $mockExpiresIn = 7200;
    
    protected function getEnvironmentSetUp($app): void
    {
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->config = require __DIR__.'/../src/config.php';
        // 模拟 AccessToken 获取接口
        Http::fake([
            'oapi.dingtalk.com/gettoken*' => function ($request) { 
                $queryString = $request->toPsrRequest()->getUri()->getQuery();
                parse_str($queryString, $queries);

                $appkey = data_get($queries, 'appkey');
                $appsecret = data_get($queries, 'appsecret');

                $appkeyInEnv = data_get($this->config, 'app.key');
                $appsecretInEnv = data_get($this->config, 'app.secret');
                // dd($queryString, $appkey, $appsecret, $appkeyInEnv, $appsecretInEnv);
                if ($appkey === $appkeyInEnv && $appsecret === $appsecretInEnv) {
                    return Http::response([
                        'errcode' => 0,
                        'access_token' => $this->mockAccessToken,
                        'expires_in' => $this->mockExpiresIn
                    ]);
                } else {
                    return Http::response([
                        'errcode' => 40096,
                        'errmsg' => "不合法的appKey或appSecret"
                    ]);
                }
            },
        ]);

        // 模拟测试接口
        Http::fake([
            'oapi.dingtalk.com/test?*' => function ($request) {
                // 提取 URL 中的 AccessToken 并返回
                // 用于检测是否正确附带 AccessToken 
                $queryString = $request->toPsrRequest()->getUri()->getQuery();
                parse_str($queryString, $queries);
                return Http::response([
                    'errcode' => 0,
                    'mock_access_token' => data_get($queries, 'access_token')
                ]);
            },
        ]);
    }

    /**
     * 调用 $instance 的私有方法 $method
     */
    public function callPrivately($instance, $method, $args = [])
    {
        $instanceReflection = new \ReflectionObject($instance);
        $method = $instanceReflection->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($instance, $args);
    }

    /**
     * 获取 $instance 的私有属性 $property
     */
    public function getPrivately($instance, $property)
    {
        $instanceRef = new \ReflectionObject($instance);
        $propRef = $instanceRef->getProperty($property);
        $propRef->setAccessible(true);
        return $propRef->getValue($instance);
    }
}
