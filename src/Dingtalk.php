<?php

namespace Keqin\Dingtalk;

class Dingtalk extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return DingtalkService::class;
    }
}
