<?php

namespace Xsanisty\Admin\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SilexStarter\SilexStarter;
use Xsanisty\Admin\TwigExtension\TwigMenuExtension;

class TwigExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->extend(
            'twig',
            function ($twigEnv, $app) {
                if (!$twigEnv->hasExtension('Xsanisty\Admin\TwigExtension\TwigMenuExtension')) {
                    $twigEnv->addExtension(new TwigMenuExtension($app['menu_manager']));
                }

                return $twigEnv;
            }
        );
    }

    public function boot(Container $app)
    {
    }
}
