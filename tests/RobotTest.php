<?php
namespace Keqin\Dingtalk\Tests;

use Keqin\Dingtalk\Robot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RobotTest extends TestCase
{
    /**
     * 一个小时后的请求算过期
     * @link https://ding-doc.dingtalk.com/doc#/serverapi2/elzz1p
     */
    public function testHasExpired()
    {
        $timestamp = Carbon::now()->valueOf();
        $zeroDotNineHourAgo = Carbon::now()->sub('0.9 hour')->valueOf();
        $oneHourAgo = Carbon::now()->sub('1 hour 1s')->valueOf();
        $robot = new Robot;
        $this->assertFalse($robot->hasExpired((int) $timestamp));
        $this->assertFalse($robot->hasExpired((int) $zeroDotNineHourAgo));
        $this->assertTrue($robot->hasExpired((int) $oneHourAgo));
    }

    /**
     * 检测是否合法请求
     * @link https://ding-doc.dingtalk.com/doc#/serverapi2/elzz1p/pvNZZ
     */
    public function testIsRequestLegal()
    {
        $fakeTimestamp = '1591911251172';
        $expiredFakeTimestamp = '1591907651171';
        $fakeSign = 'djaNG07iBZ4NQOMR0pba19SDG9pRx8yPq5s3p1X/9cs=';
        $request = new Request();
        $request->headers->add([
            'timestamp' => $fakeTimestamp,
            'sign' => $fakeSign
        ]);
        $fakeSecret = 'sec123321';
        $robot = new Robot([
            'appsecret' => $fakeSecret
        ]);
        Carbon::setTestNow(date('Y-m-d H:i:s', $fakeTimestamp));
        $this->assertTrue($robot->isRequestLegal($request));

        // 非法签名校验
        $invalidSign = 'invalid';
        $request->headers->set('sign', $invalidSign);
        $this->assertFalse($robot->isRequestLegal($request));
    }

    public function testPost()
    {
        $fakeSecret = '123';
        \Http::fake(function ($request) {
            return \Http::response([], 200);
        });
        $robot = new Robot([
            'appsecret' => $fakeSecret
        ]);
        $robot->post([], [
            'url' => 'http://example.com',
            'secret' => $fakeSecret
        ]);

        \Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                strpos($request->url(), 'http://example.com') === 0;
        });
    }

    public function testSign()
    {
        $fakeTimestamp = '1591911251172';
        $fakeSecret = 'fakesecret';
        $this->assertEquals('G9Y0gxVFLLynB9APGjGB6fLSHh5e9iDZbtu0X55eQZY=', Robot::sign($fakeTimestamp, $fakeSecret));
    }
}
