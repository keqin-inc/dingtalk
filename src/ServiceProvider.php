<?php
namespace Keqin\Dingtalk;

use Illuminate\Routing\Router;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('dingtalk.php'),
        ]);
        
        $this->publishes([
            __DIR__.'/resources/views/ddlogin.blade.php' => resource_path('views/ddlogin.blade.php'),
        ]);
        
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('dingtalk-robot-auth', RobotOutgoingMiddleware::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                AccessTokenCommand::class
            ]);
        }
    }

    public function register()
    {
        $this->app->singleton(DingtalkService::class, function ($app) {
            return new DingtalkService(config('dingtalk'));
        });
    }
}
