<?php
namespace Keqin\Dingtalk;

trait Timestamp
{
    /**
     * 返回当前时间，单位 ms
     * @return time in ms
     */
    public function timestamp(): int
    {
        return intval(now()->valueOf());
    }
}
