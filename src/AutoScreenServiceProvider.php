<?php

namespace Lsg\AutoScreen;

use Illuminate\Support\ServiceProvider;
use \Illuminate\Database\Eloquent\Builder;

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
        //宏反向注册
        Builder::macro('autoMake', function ($item = false) {
            if ($item !== false) {
                return  AutoMake::getQuery($this)->makeAutoPageList($item);
            } else {
                return  AutoMake::getQuery($this);
            }
        });
        //宏反向注册
        Builder::macro('autoUpdate', function ($item = false) {
            if ($item !== false) {
                return  AutoMake::getQuery($this)->doAutoUpdate($item);
            } else {
                return  AutoMake::getQuery($this);
            }
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
