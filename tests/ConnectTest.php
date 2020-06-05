<?php
namespace Keqin\Dingtalk\Tests;

use Keqin\Dingtalk\Connect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ConnectTest extends TestCase
{
    private $instance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->instance = $this->createInstance();
    }

    protected function createInstance($config = null)
    {
        if (is_null($config)) {
            $config = $this->config;
        }
        $instance = new Connect(data_get($config, 'connect'));
        return $instance;
    }

    /**
     * 测试签名正确实现
     */
    public function testSign()
    {
        $sign = $this->callPrivately($this->instance, 'sign', [
            $time = 1591365729929,
            $appSecret = 'appsecret321'
        ]);
        $this->assertEquals($sign, "EzVFeHififq0A7oA%2Fg3ObFbO7pm%2Bn6OTAfaEq5Lun9w%3D");
    }

    /**
     * 测试正确实现了时间戳，确保单位正确
     */
    public function testTime()
    {
        $time = $this->callPrivately($this->instance, 'time');
        $this->assertIsInt($time);
        $this->assertGreaterThan(1591365729929, $time);
        $this->assertLessThan(9999999999999, $time);
    }

    /**
     * 测试获取用户资料函数
     */
    public function testGetUserInfo()
    {
        $response = [
            "errcode" => 0,
            "errmsg" => "ok",
            "user_info" => [
                "nick" => "张三",
                "openid" => "liSii8KCxxxxx",
                "unionid" => "7Huu46kk"
            ]
        ];
        $instance = $this->createInstance();
        $url = $instance->gateway.'/sns/getuserinfo_bycode';
        Http::fake([
            $url.'*' => Http::response($response, 200)
        ]);
        $mockingTmpCode = 'abc124';
        $userInfo = $instance->getUserInfo($mockingTmpCode);
        Http::assertSent(function ($request) use ($instance, $mockingTmpCode, $url) {
            return $request->method() === 'POST' &&
                $request['tmp_auth_code'] == $mockingTmpCode &&
                strpos($request->url(), $url) === 0;
        });
        $this->assertEquals($response, $userInfo);
    }
}