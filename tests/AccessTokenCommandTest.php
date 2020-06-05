<?php
namespace Keqin\Dingtalk\Tests;

use Keqin\Dingtalk\AccessTokenCommand;
use Keqin\Dingtalk\AccessToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AccessTokenCommandTest extends TestCase
{
    /**
     * 加载 Serivce 后可访问到 command
     */
    protected function getPackageProviders($app)
    {
        return [ 
            \Keqin\Dingtalk\ServiceProvider::class
        ];
    }
    
    /**
     * 支持 \Dingtalk 的写法
     */
    protected function getPackageAliases($app)
    {
        return [
            'Dingtalk' => \Keqin\Dingtalk\Dingtalk::class
        ];
    }

    /**
     * 不输入任何参数时，必须提示当前的签名和描述
     */
    public function testCommandDescription()
    {
        $command = new AccessTokenCommand;
        $description = $this->getPrivately($command, 'description');
        $signature = $this->getPrivately($command, 'signature');
        $this->artisan('dingtalk:accesstoken')
            ->expectsOutput($description)
            ->expectsOutput($signature);
    }

    /**
     * 测试 dingtalk:accesstoken --sync
     */
    public function testSync()
    {
        // 必须设置 Facade 指定方法返回的内容为一个实例
        // 否则会报错：Error: Call to a member function sync() on string
        \Keqin\Dingtalk\Dingtalk::shouldReceive('accessToken')
            ->andReturns($this->createInstance());
        
        // 命令显示 synced!
        $this->artisan('dingtalk:accesstoken --sync')
            ->expectsOutput('synced!')
            ->assertExitCode(0);

        // 同时缓存了最新的 Token
        $cacheKeyname = data_get($this->config, 'app.cache_keyname');
        Cache::shouldReceive('put')
            ->with($cacheKeyname, $this->mockAccessToken, $this->mockExpiresIn);
        
    }

    /**
     * 测试 dingtalk:accesstoken --show
     */
    public function testShow()
    {
        \Keqin\Dingtalk\Dingtalk::shouldReceive('accessToken')
            ->andReturns($this->createInstance());
        
        // 命令正确显示 key
        $appKey = data_get($this->config, 'app.key');
        $this->artisan('dingtalk:accesstoken --show')
            ->expectsOutput('AppKey: '.$appKey)
            ->expectsOutput('AccessToken: '.$this->mockAccessToken)
            ->assertExitCode(0);
    }

    private function createInstance()
    {
        $instance = new AccessToken();
        $instance->config(data_get($this->config, 'app'));
        return $instance;
    }
}
