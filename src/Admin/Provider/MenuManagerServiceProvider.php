<?php

namespace Xsanisty\Admin\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use SilexStarter\SilexStarter;
use Xsanisty\Admin\Menu\MenuManager;

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
            $app->bind('Xsanisty\Admin\Menu\MenuManager', 'menu_manager');
        }
    }

    public function boot(Application $app)
    {
    }
}
