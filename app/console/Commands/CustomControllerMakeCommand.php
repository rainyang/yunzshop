<?php

namespace app\console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Console\ControllerMakeCommand;

class CustomControllerMakeCommand extends ControllerMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:yz-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make yunshop custom controller class';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('model')) {
            $modelClass = $this->parseModel($this->option('model'));

            $replace = [
                'DummyFullModelClass' => $modelClass,
                'DummyModelClass' => class_basename($modelClass),
                'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            ];
        }

        $replace["use app\common\components\BaseController;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }
}
