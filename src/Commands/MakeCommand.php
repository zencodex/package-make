<?php

namespace Laravel\Package\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Laravel\Package\Support\Stub;
use Illuminate\Filesystem\Filesystem;
use Laravel\Package\Support\Config\GenerateConfigReader;

class MakeCommand extends Command
{

    /**
     * The laravel filesystem instance.
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * The module instance.
     *
     * @var \Laravel\Package\Manager
     */
    protected $module;

    /**
     * @var string
     */
    private $packageName;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'package:make {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new laravel package/module/plugin';

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
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        $this->packageName = $this->argument('name');
        throw_if(empty($this->packageName), 'argument name is empty');

        app('modules')->setPackageName($this->packageName);
        $this->filesystem = $this->laravel['files'];
        $this->module = $this->laravel['modules'];

        $this->generateFolders();
        $this->generateFiles();
        $this->generateResources();

        $this->info("Package [{$this->packageName}] created successfully");
    }

    /**
     * Get the name of module will created. By default in studly case.
     *
     * @return string
     */
    public function getPackageName()
    {
        return Str::studly($this->packageName);
    }

    /**
     * Get the list of folders will created.
     *
     * @return array
     */
    public function getFolders()
    {
        return config('modules.paths.generator');
    }

    /**
     * Generate the folders.
     */
    public function generateFolders()
    {
        foreach ($this->getFolders() as $key => $folder) {
            $folder = GenerateConfigReader::read($key);

            if ($folder->generate() === false) {
                continue;
            }

            $path = config('modules.paths.modules') . '/' . $this->getPackageName() . '/' . $folder->getPath();

            $this->filesystem->makeDirectory($path, 0755, true);
            if (config('modules.stubs.gitkeep')) {
                $this->generateGitKeep($path);
            }
        }
    }

    /**
     * Generate git keep to the specified path.
     *
     * @param string $path
     */
    public function generateGitKeep($path)
    {
        $this->filesystem->put($path . '/.gitkeep', '');
    }

    /**
     * Get the list of files will created.
     *
     * @return array
     */
    public function getFiles()
    {
        return config('modules.stubs.files');
    }

    /**
     * Generate the files.
     */
    public function generateFiles()
    {
        foreach ($this->getFiles() as $stub => $file) {
            $path = config('modules.paths.modules') . '/' . $this->getPackageName() . '/' . $file;

            if (!$this->filesystem->isDirectory($dir = dirname($path))) {
                $this->filesystem->makeDirectory($dir, 0775, true);
            }

            $this->filesystem->put($path, $this->getStubContents($stub));

            $this->info("Created : {$path}");
        }
    }

    /**
     * Generate some resources.
     */
    public function generateResources()
    {
        if (GenerateConfigReader::read('seeder')->generate() === true) {
            $this->call('package:make-seed', [
                'name'     => $this->getPackageName(),
                'module'   => $this->getPackageName(),
                '--master' => true,
            ]);
        }

        if (GenerateConfigReader::read('provider')->generate() === true) {
            $this->call('package:make-provider', [
                'name'     => $this->getPackageName() . 'ServiceProvider',
                'module'   => $this->getPackageName(),
                '--master' => true,
            ]);
            $this->call('package:make-route-provider', [
                'module' => $this->getPackageName(),
            ]);
        }

        if (GenerateConfigReader::read('controller')->generate() === true) {
            $this->call('package:make-controller', [
                'controller' => $this->getPackageName() . 'Controller',
                'module'     => $this->getPackageName(),
            ]);
        }
    }

    /**
     * Get class name.
     *
     * @return string
     */
    public function getClass()
    {
        return class_basename($this->argument('name'));
    }

    /**
     * Get class namespace.
     *
     * @param \Laravel\Package\Module $module
     *
     * @return string
     */
    public function getClassNamespace($module)
    {
        $extra = str_replace($this->getClass(), '', $this->argument('name'));
        $extra = str_replace('/', '\\', $extra);

        $namespace = config('modules.namespace');
        $namespace .= '\\' . $this->getPackageName();
        $namespace .= '\\' . $extra;

        $namespace = str_replace('/', '\\', $namespace);
        return trim($namespace, '\\');
    }

    /**
     * Get the contents of the specified stub file by given stub name.
     *
     * @param $stub
     *
     * @return string
     */
    protected function getStubContents($stub)
    {
        return (new Stub(
            '/' . $stub . '.stub',
            $this->getReplacement($stub)
        )
        )->render();
    }

    /**
     * Get array replacement for the specified stub.
     *
     * @param $stub
     *
     * @return array
     */
    protected function getReplacement($stub)
    {
        $replacements = config('modules.stubs.replacements');

        if (!isset($replacements[$stub])) {
            return [];
        }

        $keys = $replacements[$stub];

        $replaces = [];

        if ($stub === 'json' || $stub === 'composer') {
            if (in_array('PROVIDER_NAMESPACE', $keys, true) === false) {
                $keys[] = 'PROVIDER_NAMESPACE';
            }
        }
        foreach ($keys as $key) {
            if (method_exists($this, $method = 'get' . ucfirst(Str::studly(strtolower($key))) . 'Replacement')) {
                $replaces[$key] = $this->$method();
            } else {
                $replaces[$key] = null;
            }
        }

        return $replaces;
    }

    /**
     * Get the module name in lower case.
     *
     * @return string
     */
    protected function getLowerNameReplacement()
    {
        return strtolower($this->getPackageName());
    }

    /**
     * Get the module name in studly case.
     *
     * @return string
     */
    protected function getStudlyNameReplacement()
    {
        return $this->getPackageName();
    }

    /**
     * Get replacement for $VENDOR$.
     *
     * @return string
     */
    protected function getVendorReplacement()
    {
        return $this->module->config('composer.vendor');
    }

    /**
     * Get replacement for $MODULE_NAMESPACE$.
     *
     * @return string
     */
    protected function getModuleNamespaceReplacement()
    {
        return str_replace('\\', '\\\\', $this->module->config('namespace'));
    }

    /**
     * Get replacement for $AUTHOR_NAME$.
     *
     * @return string
     */
    protected function getAuthorNameReplacement()
    {
        return $this->module->config('composer.author.name');
    }

    /**
     * Get replacement for $AUTHOR_EMAIL$.
     *
     * @return string
     */
    protected function getAuthorEmailReplacement()
    {
        return $this->module->config('composer.author.email');
    }

    protected function getProviderNamespaceReplacement(): string
    {
        return str_replace('\\', '\\\\', GenerateConfigReader::read('provider')->getNamespace());
    }
}
