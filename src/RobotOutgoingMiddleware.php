<?php
namespace Keqin\Dingtalk;
use Illuminate\Auth\AuthenticationException;

class RobotOutgoingMiddleware
{
    /**
     * 检查是否为 Outgoing Robot 请求
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string $appSecret 机器人的 AppSecret
     * @return mixed
     */
    public function handle($request, \Closure $next, $appSecret)
    {
        $robot = new Robot([
            'appsecret' => $appSecret
        ]);
        if ($robot->isRequestLegal($request)) {
            return $next($request);
        } else {
            throw new AuthenticationException();
        }
    }
}