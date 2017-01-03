<?php

namespace Xsanisty\Admin\TwigExtension;

use Xsanisty\Admin\Menu\MenuManager;
use Twig_Extension;
use Twig_SimpleFunction;

class TwigMenuExtension extends Twig_Extension
{
    protected $menu;

    public function __construct(MenuManager $menu)
    {
        $this->menu = $menu;
    }

    public function getName()
    {
        return 'silex-starter-menu-ext';
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('add_menu', [$this, 'addMenu'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('render_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('set_active_menu', [$this, 'setActiveMenu'], ['is_safe' => ['html']]),
        ];
    }

    public function renderMenu($menu, array $option = [])
    {
        return $this->menu->render($menu, $option);
    }

    public function setActiveMenu($menuId)
    {
        list($menuGroup, $menuItem) = explode('.', $menuId, 2);

        $this->menu->get($menuGroup)->setActive($menuItem);

        return;
    }

    public function addMenu($group, $name, array $attributes)
    {
        $this->menu->get($group)->createItem($name, $attributes);

        return;
    }
}
