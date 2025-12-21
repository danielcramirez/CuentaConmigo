<?php

namespace App;

use Illuminate\Foundation\Application;

class Application extends \Illuminate\Foundation\Application
{
    public function __construct($basePath = null)
    {
        parent::__construct($basePath);

        $this->useAppPath($basePath ? "{$basePath}/app" : null);
        $this->useBootstrapPath($basePath ? "{$basePath}/bootstrap" : null);
        $this->useConfigPath($basePath ? "{$basePath}/config" : null);
        $this->useDatabasePath($basePath ? "{$basePath}/database" : null);
        $this->useResourcePath($basePath ? "{$basePath}/resources" : null);
        $this->useStoragePath($basePath ? "{$basePath}/storage" : null);
    }
}
