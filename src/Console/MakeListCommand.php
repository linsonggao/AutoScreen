<?php

namespace Lsg\AutoScreen\Console;

use Illuminate\Console\GeneratorCommand;

class MakeListCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'task:make_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成List对象类';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'List';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        // Implement getStub() method.
        return $this->laravel->basePath('vendor/lsg/auto-screen/src/Stubs/list.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Lists';
    }
}
