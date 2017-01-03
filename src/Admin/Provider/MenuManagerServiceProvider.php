<?php

namespace SilexStarter\Admin\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use SilexStarter\SilexStarter;
use SilexStarter\Admin\Menu\MenuManager;

class MenuManagerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['menu_manager'] = $app->share(
            function () {
                return new MenuManager();
            }
        );

        if ($app instanceof SilexStarter) {
            $app->bind('SilexStarter\Admin\Menu\MenuManager', 'menu_manager');
        }
    }

    public function boot(Application $app)
    {
    }
}
