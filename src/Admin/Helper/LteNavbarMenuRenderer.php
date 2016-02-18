<?php

namespace Xsanisty\Admin\Helper;

use Exception;
use SilexStarter\Contracts\MenuRendererInterface;
use SilexStarter\Menu\MenuContainer;
use SilexStarter\Menu\MenuItem;
use SilexStarter\Asset\AssetManager;
use Cartalyst\Sentry\Users\UserInterface;

class LteNavbarMenuRenderer implements MenuRendererInterface
{
    protected $assetManager;
    protected $options;
    protected $currentUser;
    protected $renderer;

    public function __construct(AssetManager $assetManager, UserInterface $currentUser = null, array $options = [])
    {
        $this->assetManager = $assetManager;
        $this->options      = $options;
        $this->currentUser  = $currentUser;

        $this->addUserMenuRenderer();
        $this->addGeneralMenuRenderer();
        $this->addNotificationMenuRenderer();
    }

    public function render(MenuContainer $menu)
    {
        return $this->generateHtml($menu);
    }

    public function addRenderer($name, callable $renderer)
    {
        $this->renderer[$name] = $renderer;
    }

    protected function generateHtml(MenuContainer $menu)
    {
        $html       = '<ul class="nav navbar-nav">%s</ul>';
        $itemList   = '';

        $menuItems  = $menu->getItems();
        unset($menuItems['user']);

        $menuItems['user'] = $menu->getItem('user');

        foreach ($menuItems as $item) {
            $renderer = $item->getMetaAttribute('renderer');

            if ($renderer && isset($this->renderer[$renderer])) {
                $rendererCallback = $this->renderer[$renderer];
            } else {
                $rendererCallback = $this->renderer['general-menu-renderer'];
            }

            $itemList .= $rendererCallback($item);
        }

        return sprintf($html, $itemList);
    }

    /**
     * Render menu specified for user account menu
     */
    protected function addUserMenuRenderer()
    {
        $this->renderer['user-menu-renderer'] = function (MenuItem $item) {
            $subMenu        = $item->getChildContainer()->getItems();
            $logoutMenu     = $subMenu['user-logout'];
            $userMenu       = $subMenu['user-header'];
            $accountMenu    = isset($subMenu['my-account']) ? $subMenu['my-account'] : false;
            $subMenuTpl     = '<li class="user-body"> %s </li>';
            $compiledSubMenu= '';

            unset($subMenu['user-logout'], $subMenu['user-header'], $subMenu['my-account']);

            if ($subMenu) {
                $subMenu = array_chunk($subMenu, 3);

                foreach ($subMenu as $subMenuRow) {
                    $row    = '<div class="row"> %s </div>';
                    $col    = '<div class="col-xs-%s text-center"><a href="%s">%s</a></div>';
                    $grid   = 12 / count($subMenuRow);
                    $gridMenu = '';

                    foreach ($subMenuRow as $subItem) {
                        $gridMenu .= sprintf($col, $grid, $subItem->url, $subItem->label);
                    }

                    $compiledSubMenu .= sprintf($row, $gridMenu);
                }

                $compiledSubMenu = sprintf($subMenuTpl, $compiledSubMenu);
            }

            $compiledMainMenu = '
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="'.$userMenu->icon.'" class="user-image" alt="User Image">
                        <span class="hidden-xs">'.$userMenu->label.'</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="'.$userMenu->icon.'" class="img-circle" alt="User Image">
                            <p>'.$userMenu->label.'</p>
                        </li>'.
                        $compiledSubMenu
                        .'<li class="user-footer">'.
                            (
                                $accountMenu ?
                                '<div class="pull-left">
                                    <a href="'.$accountMenu->url.'" class="btn btn-default btn-flat">'.$accountMenu->label.'</a>
                                </div>' : ''
                            )
                            .'<div class="pull-right">
                                <a href="'.$logoutMenu->url.'" class="btn btn-default btn-flat">'.$logoutMenu->label.'</a>
                            </div>
                        </li>
                    </ul>
                </li>
            ';

            return $compiledMainMenu;
        };
    }

    /**
     * Render notification type of menu
     */
    protected function addNotificationMenuRenderer()
    {
        $this->renderer['notification-menu-renderer'] = function (MenuItem $item) {
            $containerTemplate = '
                <li class="dropdown notifications-menu notification">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-'.$item->icon.'"></i>'.
                        (
                            $item->getMetaAttribute('counter') > 0
                            ? '<span class="label label-danger">'.$item->getMetaAttribute('counter').'</span>'
                            : ''
                        ).'
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have '.$item->getMetaAttribute('counter').' notifications</li>
                        <li>
                            <ul class="menu notification-menu">
                                %s
                            </ul>
                        </li>
                        <li class="footer"><a href="'.$item->getMetaAttribute('channel').'">View all</a></li>
                    </ul>
                </li>
            ';

            $items          = $item->getChildContainer()->getItems();
            $menuItem       = '';
            $footerItem     = '';
            $itemTemplate   = '<li><a href="%s"> <i class="fa fa-%s text-aqua"></i> %s </a></li>';
            $footerTemplate = '<li class="footer"><a href="%s"> <i class="fa fa-%s"></i> %s </a></li>';


            foreach ($items as $item) {
                $menuItem .= sprintf(
                    $itemTemplate,
                    $item->url,
                    $item->icon,
                    $item->label
                );
            }

            $compiledMenu = sprintf($containerTemplate, $menuItem);

            return $compiledMenu;
        };
    }

    protected function addGeneralMenuRenderer()
    {
        $this->renderer['general-menu-renderer'] = function (MenuItem $item) {

        };
    }
}
