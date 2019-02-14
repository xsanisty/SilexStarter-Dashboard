<?php

namespace Xsanisty\Admin\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SilexStarter\SilexStarter;
use Xsanisty\Admin\Menu\MenuManager;

class MenuManagerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
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

    public function boot(Container $app)
    {
    }
}
