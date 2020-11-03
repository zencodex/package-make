<?php

namespace Laravel\Package\Traits;

trait ModuleCommandTrait
{
    /**
     * Get the module name.
     *
     * @return string
     */
    public function getModuleName()
    {
        $module = app('modules')->findOrFail();
        return $module->getStudlyName();
    }
}
