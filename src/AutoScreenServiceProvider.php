<?php

namespace Lsg\AutoScreen;

use Illuminate\Support\ServiceProvider;

class AutoScreenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // 单例绑定服务---可以门面调用

        $this->app->singleton('auto-screen', function () {
            return new  AutoScreen;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/automake.php' => config_path('automake.php'), // 发布配置文件到 laravel 的config 下         
        ]);
    }
    /**
     * 获取服务
     *
     * @return array
     */
    public function provides()
    {
        return ['auto-screen'];
    }
}
