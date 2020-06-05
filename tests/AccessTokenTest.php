<?php
namespace Keqin\Dingtalk\Tests;

use Keqin\Dingtalk\AccessToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AccessTokenTest extends TestCase
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
        $instance = new AccessToken();
        $instance->config(data_get($config, 'app'));
        return $instance;
    }

    /**
     * 测试 fetch 正确返回 AccessToken
     */
    public function testFetch()
    {
        $resData = $this->instance->fetch();
        $this->assertEquals(data_get($resData, 'access_token'), $this->mockAccessToken);
    }

    /**
     * 测试错误的 secret 是否能正常触发异常
     */
    public function testInvalidkeyFetch()
    {
        $this->expectException(\Keqin\Dingtalk\Exception::class);
        $this->expectExceptionCode(40096);
        $config = $this->config;
        data_set($config, 'app.secret', 'InvalidSecret');
        $this->createInstance($config)
            ->fetch();
    }

    /**
     * 测试 Cache 是否正确设置
     */
    public function testSync()
    {
        $cacheKeyname = data_get($this->config, 'app.cache_keyname');
        Cache::shouldReceive('put')
            ->with($cacheKeyname, $this->mockAccessToken, $this->mockExpiresIn);
        $this->instance->sync();
    }

    /**
     * 测试设置 gateway
     */
    public function testGateway()
    {
        $newGateway = 'https://example.com';
        Http::fake();
        $this->createInstance()
            ->gateway($newGateway)
            ->fetch();
        Http::assertSent(function ($request) use ($newGateway) {
            return strpos($request->url(), $newGateway) === 0;
        });
    }
}
