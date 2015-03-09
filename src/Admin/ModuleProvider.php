<?php

namespace Xsanisty\Admin;

use Silex\Application;
use SilexStarter\Module\ModuleResource;
use SilexStarter\Module\ModuleInfo;
use SilexStarter\Contracts\ModuleProviderInterface;

class ModuleProvider implements ModuleProviderInterface{

    protected $app;

    public function __construct(Application $app){
        $this->app = $app;
    }

    public function getInfo(){
        return new ModuleInfo([
            'author_name'   => 'Xsanisty Development Team',
            'author_email'  => 'developers@xsanisty.com',
            'repository'    => 'https://github.com/xsanisty/SilexStarter-admin',
            'name'          => 'Xsanisty Admin Module',
        ]);
    }

    public function getModuleAccessor(){
        return 'xsanisty-admin';
    }

    public function getRequiredModules(){
        return [];
    }

    public function getResources(){
        return new ModuleResource([
            'routes'        => 'Resources/routes.php',
            'middlewares'   => 'Resources/middlewares.php',
            'views'         => 'Resources/views/'.$this->app['config']->get('@xsanisty-admin::config.template'),
            'controllers'   => 'Controller',
            'config'        => 'Resources/config',
            'assets'        => 'Resources/assets'
        ]);
    }

    public function register(){
        $this->app->registerServices(
            $this->app['config']['xsanisty-admin::services']
        );
    }

    public function boot(){

    }
}