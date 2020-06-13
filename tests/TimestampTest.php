<?php
namespace Keqin\Dingtalk\Tests;

class TimestampTest extends TestCase
{
    use \Keqin\Dingtalk\Timestamp;

    /**
     * 测试正确实现了时间戳，确保单位正确
     */
    public function testTime()
    {
        $time = $this->timestamp();
        $this->assertIsInt($time);
        $this->assertGreaterThan(1591365729929, $time);
        $this->assertLessThan(9999999999999, $time);
    }
}
