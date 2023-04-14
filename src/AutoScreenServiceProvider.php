<?php

namespace Lsg\AutoScreen;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Lsg\AutoScreen\Support\CustomPaginator;

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
            return new AutoScreen;
        });
        //宏反向注册
        Builder::macro('autoMake', function (...$item) {
            if ($item !== false) {
                return AutoMake::getQuery($this)->makeAutoPageList(...$item);
            } else {
                return AutoMake::getQuery($this);
            }
        });
        //宏反向注册
        Builder::macro('autoList', function (...$item) {
            if ($item !== false) {
                return AutoMake::getQuery($this)->makeCustomPageList(...$item);
            } else {
                return AutoMake::getQuery($this);
            }
        });
        //宏反向注册
        Builder::macro('makeList', function (...$item) {
            if ($item !== false) {
                return AutoMake::getQuery($this)->makeList(...$item);
            } else {
                return AutoMake::getQuery($this);
            }
        });
        //宏反向注册
        Builder::macro('makeListArray', function (...$item) {
            if ($item !== false) {
                return AutoMake::getQuery($this)->makeList(...$item)['list']->toArray();
            } else {
                return AutoMake::getQuery($this);
            }
        });
        //宏反向注册
        Builder::macro('autoUpdate', function (...$item) {
            if ($item !== false) {
                return AutoMake::getQuery($this)->doAutoUpdate(...$item);
            } else {
                return AutoMake::getQuery($this);
            }
        });
        //宏反向注册
        Builder::macro('makeUpdate', function (...$item) {
            if ($item !== false) {
                return AutoMake::getQuery($this)->doAutoUpdate(...$item);
            } else {
                return AutoMake::getQuery($this);
            }
        });
        //宏反向注册
        Builder::macro('makeCreate', function (...$item) {
            if ($item !== false) {
                return AutoMake::getQuery($this)->doAutoCreate(...$item);
            } else {
                return AutoMake::getQuery($this);
            }
        });
        //宏反向注册
        Builder::macro('autoQuery', function () {
            return AutoMake::makeAutoQuery();
        });
        //宏反向注册
        Builder::macro('makeCount', function (...$item) {
            if ($item !== false) {
                return AutoMake::getQuery($this)->makeCount(...$item);
            } else {
                return AutoMake::getQuery($this);
            }
        });
        Builder::macro('customPage', function (...$item) {
            return new CustomPaginator($this->paginate(...$item));
        });
        //宏反向注册
        Builder::macro('makeValidate', function (...$item) {
            if ($item !== false) {
                return AutoMake::getQuery($this)->makeValidate(...$item);
            } else {
                return AutoMake::getQuery($this);
            }
        });

        $this->commands([MakeValidateCommand::class]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/automake.php'     => config_path('automake.php'), // 发布配置文件到 laravel 的config 下
            __DIR__ . '/../config/makeValidate.php' => config_path('makeValidate.php'), // 发布配置文件到 laravel 的config 下
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
