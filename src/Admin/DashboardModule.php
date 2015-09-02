<?php

namespace Xsanisty\Admin;

use Silex\Application;
use SilexStarter\Module\ModuleResource;
use SilexStarter\Module\ModuleInfo;
use SilexStarter\Contracts\ModuleProviderInterface;
use Xsanisty\Admin\Helper\SidebarMenuRenderer;
use Xsanisty\Admin\Helper\NavbarMenuRenderer;

class DashboardModule implements ModuleProviderInterface
{
    const INIT = 'dashboard.init';

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getInfo()
    {
        return new ModuleInfo(
            [
                'author_name'   => 'Xsanisty Development Team',
                'author_email'  => 'developers@xsanisty.com',
                'repository'    => 'https://github.com/xsanisty/SilexStarter-Dashboard',
                'name'          => 'SilexStarter Base Dashboard Module',
                'description'   => 'Provide basic dashboard page and login/logout function'
            ]
        );
    }

    public function getModuleIdentifier()
    {
        return 'silexstarter-dashboard';
    }

    public function getRequiredModules()
    {
        return [];
    }

    public function getResources()
    {
        return new ModuleResource(
            [
                'routes'        => 'Resources/routes.php',
                'middlewares'   => 'Resources/middlewares.php',
                'views'         => 'Resources/views',
                'controllers'   => 'Controller',
                'config'        => 'Resources/config',
                'assets'        => 'Resources/assets'
            ]
        );
    }

    public function register()
    {
        $this->app->registerServices(
            $this->app['config']['@silexstarter-dashboard.services']
        );

        $menu   = $this->app['menu_manager']->create('admin_sidebar');
        $navbar = $this->app['menu_manager']->create('admin_navbar');
        $self   = $this;

        $this->app['dispatcher']->addListener(
            DashboardModule::INIT,
            function () use ($menu, $navbar, $self) {
                $menu->setRenderer(
                    new SidebarMenuRenderer(
                        $this->app['asset_manager'],
                        $this->app['sentry']->getUser(),
                        $this->app['config']['@silexstarter-dashboard.config']
                    )
                );
                $navbar->setRenderer(new NavbarMenuRenderer);

                $self->registerNavbarMenu();
            },
            5
        );
    }


    /**
     * Register menu item to navbar menu
     */
    protected function registerNavbarMenu()
    {
        $user   = $this->app['sentry']->getUser();
        $name   = $user ? $user->first_name.' '.$user->last_name : '';
        $email  = $user ? $user->email : '';
        $name   = trim($name) ? $name : $email;


        $menu = $this->app['menu_manager']->get('admin_navbar')->createItem(
            'user',
            [
                'icon'  => 'user',
                'url'   => '#user',
            ]
        );

        $menu->addChildren(
            'user-header',
            [
                'label' => $name,
                'class' => 'header'
            ]
        );

        $menu->addChildren('logout-divider', [ 'class' => 'divider' ]);

        $menu->addChildren(
            'user-logout',
            [
                'label' => 'Logout',
                'class' => 'link',
                'icon'  => 'sign-out',
                'url'   => 'admin.logout'
            ]
        );
    }

    public function boot()
    {

    }
}
