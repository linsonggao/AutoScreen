<?php

namespace Lsg\AutoScreen;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Lsg\AutoScreen\Console\MakeListCommand;
use Lsg\AutoScreen\Console\MakeValidateCommand;
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
        $this->dbDebug();
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
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([MakeValidateCommand::class, MakeListCommand::class]);
        $this->publishes([
            __DIR__ . '/../config/automake.php'     => config_path('automake.php'), // 发布配置文件到 laravel 的config 下
            __DIR__ . '/../config/makeValidate.php' => config_path('makeValidate.php'), // 发布配置文件到 laravel 的config 下
        ]);
    }

    private function dbDebug()
    {
        // 记录DB日志
        if (config('automake.sql_log_debug', false)) {
            DB::listen(function ($query) {
                $location = collect(debug_backtrace())->filter(function ($trace) {
                    return !str_contains($trace['file'] ?? '', 'vendor/');
                })->first(); // grab the first element of non vendor/ calls
                //$bindings = implode(', ', $query->bindings); // format the bindings as string

                try {
                    //code...
                    $record = str_replace('?', '"' . '%s' . '"', $query->sql);
                    $record = vsprintf($record, $query->bindings);
                    $record = str_replace('\\', '', $record);
                    $sec_time = $query->time = $query->time / 1000;
                    $location['file'] = $location['file'] ?? '';
                    $location['line'] = $location['line'] ?? '';
                    Log::channel('sql')->info('------------------------------------------------');
                    Log::channel('sql')->info('URL: ' . request()->url());
                    Log::channel('sql')->info('------------------------------------------------');
                    Log::channel('sql')->info("
                    ------------
                    Sql: {$record}
                    Time: {$sec_time}秒
                    File: {$location['file']}
                    Line: {$location['line']}
                    ------------
                ");
                } catch (\Throwable $th) {
                    //throw $th;
                    Log::channel('sql')->info('---捕获异常sql---', [$query->sql]);
                }
            });
        }
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
