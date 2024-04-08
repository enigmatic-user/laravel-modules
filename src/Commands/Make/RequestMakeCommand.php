<?php

namespace Nwidart\Modules\Commands\Make;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class RequestMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected string $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form request class for the specified module.';

    public function getDefaultNamespace(): string
    {
        return config('modules.paths.generator.request.namespace')
            ?? ltrim(config('modules.paths.generator.request.path', 'Http/Requests'), config('modules.paths.app_folder', ''));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form request class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    protected function getTemplateContents(): string
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/request.stub', [
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS'     => $this->getClass(),
        ]))->render();
    }

    protected function getDestinationFilePath(): string
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $requestPath = GenerateConfigReader::read('request');

        return $path . $requestPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    private function getFileName(): string
    {
        return Str::studly($this->argument('name'));
    }
}
