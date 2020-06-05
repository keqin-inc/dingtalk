<?php
namespace Keqin\Dingtalk\Tests;

class DingtalkServiceTest extends TestCase
{
    protected $dingtalk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dingtalk = new \Keqin\Dingtalk\DingtalkService($this->config);
    }

    public function testBuildUrl()
    {
        $url = $this->dingtalk->buildUrl('test');
        $urlWithQueryString = $this->dingtalk->buildUrl('test', [
            'a' => 'b'
        ]);
        $this->assertEquals($url, 'https://oapi.dingtalk.com/test?access_token=mockaccesstoken123');
        $this->assertEquals($urlWithQueryString, 'https://oapi.dingtalk.com/test?a=b&access_token=mockaccesstoken123');
    }

    public function testGet()
    {
        $res = $this->dingtalk->get('test')->json();
        $this->assertEquals($res['mock_access_token'], $this->mockAccessToken);
    }

    public function testPost()
    {
        $res = $this->dingtalk->post('test', [])->json();
        $this->assertEquals($res['mock_access_token'], $this->mockAccessToken);
    }
}
