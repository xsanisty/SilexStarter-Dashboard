<?php

namespace Xsanisty\Admin\Helper;

use SilexStarter\Menu\MenuRendererInterface;
use SilexStarter\Menu\MenuContainer;

class NavbarMenuRenderer implements MenuRendererInterface
{
    protected $menu;

    public function render(MenuContainer $menu)
    {
        return '<ul></ul>';
    }
}
