<?php
namespace Keqin\Dingtalk\Tests;

use Keqin\Dingtalk\RobotOutgoingMiddleware;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RobotOutgoingMiddlewareTest extends TestCase
{
    public function testHandle()
    {
        $fakeTimestamp = '1591911251172';
        $expiredFakeTimestamp = '1591907651171';
        $fakeSign = 'djaNG07iBZ4NQOMR0pba19SDG9pRx8yPq5s3p1X/9cs=';
        $fakeSecret = 'sec123321';

        // 有效的 Request
        $validRequest = new Request();
        $validRequest->headers->add([
            'timestamp' => $fakeTimestamp,
            'sign' => $fakeSign
        ]);

        // 无效的 Request
        $invalidRequest = new Request();
        $invalidRequest->headers->add([
            'timestamp' => $expiredFakeTimestamp,
            'sign' => $fakeSign
        ]);

        // 避免触发过期，设置当前系统时间
        Carbon::setTestNow(date('Y-m-d H:i:s', $fakeTimestamp));

        $middleware = new RobotOutgoingMiddleware;
        $next = function() {
            return 'Accessible';
        };
        $responseText = $middleware->handle($validRequest, $next, $fakeSecret);
        $this->assertEquals('Accessible', $responseText);
        $this->expectException(\Illuminate\Auth\AuthenticationException::class);
        $invalidResult = $middleware->handle($invalidRequest, $next, $fakeSecret);
    }
}
