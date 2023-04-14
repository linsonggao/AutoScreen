<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lsg\AutoScreen\Middleware\ValidateMake;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * 宫颈癌相关人群同步
 */
class MakeValidateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:make_validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新验证器路由缓存';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $configKey = [];
        $config = config('makeValidate');

        foreach ($config as $key => $configKeys) {
            $configKey[] = $configKeys[0][0];
        }
        $configKey = array_unique($configKey);
        // 准备进度条
        /** @var ProgressBar $bar */
        $bar = tap(
            $this->output->createProgressBar(
                count($configKey)
            ),
            fn (ProgressBar $bar) => $bar->start()
        );

        foreach ($configKey as $key => $nowActionKey) {
            (new ValidateMake())->makeValidateCache($nowActionKey);
            $bar->advance();
        }
    }
}
