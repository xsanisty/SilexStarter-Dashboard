<?php

namespace Xsanisty\Admin\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Xsanisty\Admin\Helper\SidebarMenuRenderer;

class SidebarMenuServiceProvider implements ServiceProviderInterface{

    public function register(Application $app){
        $menu   = $this->app['menu_manager']->create('admin.sidebar');

        $menu->setRenderer(new SidebarMenuRenderer);
    }

    public function boot(Application $app){

    }
}