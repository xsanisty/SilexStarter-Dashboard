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
        $self   = $this;

        $this->app->registerServices(
            $this->app['config']['@silexstarter-dashboard.services']
        );

        $this->app['dispatcher']->addListener(
            DashboardModule::INIT,
            function () use ($self) {
                $menu   = $self->app['menu_manager']->create('admin_sidebar');
                $navbar = $self->app['menu_manager']->create('admin_navbar');

                $menu->setRenderer(
                    new SidebarMenuRenderer(
                        $self->app['asset_manager'],
                        $self->app['sentry']->getUser(),
                        $self->app['config']['@silexstarter-dashboard.config']
                    )
                );
                $navbar->setRenderer(new NavbarMenuRenderer);

                $self->registerNavbarMenu();
                Asset::exportVariable('base_url', Url::path('/', true));
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
                'meta'  => ['type' => 'header']

            ]
        );

        $menu->addChildren('logout-divider', ['meta' => ['type' => 'divider']]);

        $menu->addChildren(
            'user-logout',
            [
                'label' => 'Logout',
                'icon'  => 'sign-out',
                'url'   => Url::to('admin.logout'),
                'meta'  => ['type' => 'link']
            ]
        );
    }

    public function boot()
    {

    }
}
