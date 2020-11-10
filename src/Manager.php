<?php

namespace Laravel\Package;

use Illuminate\Support\Str;

class Manager
{

    /**
     * @var string
     */
    private $packageName;

    /**
     * @param string $packageName
     */
    public function setPackageName($packageName)
    {
        $this->packageName = $packageName;
    }

    /**
     * @param string $key
     * @param null|string $default
     * @return mixed
     */
    public function config(string $key, $default = null)
    {
        return config('modules.' . $key, $default);
    }

    /**
     * Get name in lower case.
     *
     * @return string
     */
    public function getLowerName(): string
    {
        return strtolower($this->packageName);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function findOrFail(string $name = '')
    {
        // Just a trick, for compatible
        return $this;
    }

    public function getStudlyName()
    {
        return Str::studly($this->packageName);
    }

    /**
     * Handle call __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getStudlyName();
    }

    /**
     * Get module path for a specific module.
     *
     * @param $module
     *
     * @return string
     */
    public function getModulePath($module)
    {
        return $this->getPath() . '/' . Str::studly($module) . '/';
    }

    /**
     * @return string
     */
    public function getUsedNow()
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    public function getPath() : string
    {
        return $this->config('paths.modules');
    }

    /**
     * @param string $module
     * @return bool
     */
    public function isExist($module)
    {
        return file_exists($this->getModulePath($module));
    }

}
