<?php

namespace Xsanisty\Admin\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use SilexStarter\SilexStarter;
use Xsanisty\Admin\TwigExtension\TwigMenuExtension;

class TwigExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->extend(
            'twig',
            function ($twigEnv, $app) {
                if ($twigEnv->hasExtension('Xsanisty\Admin\TwigExtension\TwigMenuExtension')) {
                    $twigEnv->addExtension(new TwigMenuExtension($app['menu_manager']));
                }

                return $twigEnv;
            }
        );
    }

    public function boot(Application $app)
    {
    }
}
