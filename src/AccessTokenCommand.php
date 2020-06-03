<?php
namespace Keqin\Dingtalk;
use Illuminate\Console\Command;

class AccessTokenCommand extends Command
{
    protected $signature = 'dingtalk:accesstoken {--sync} {--show}';
    protected $description = '钉钉 AccessToken 定时刷新';
    
    public function handle()
    {
        $shouldSync = $this->option('sync');
        if ($shouldSync) {
            $token = \Dingtalk::accessToken();
            $token->sync();
            $this->info('synced!');
        }
        $shouldShow = $this->option('show');
        if ($shouldShow) {
            $token = \Dingtalk::accessToken();
            $this->info('AppKey: ' . $token->appKey);
            $this->info('AccessToken: ' . strval($token));
        }
    }
}