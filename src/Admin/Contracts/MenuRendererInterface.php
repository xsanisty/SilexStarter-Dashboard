<?php

namespace Xsanisty\Admin\Contracts;

use Xsanisty\Admin\Menu\MenuContainer;

interface MenuRendererInterface
{
    /**
     * Render the menu collection set.
     *
     * @param SilexStarter\Admin\Menu\MenuContainer $menu the menu collection set
     *
     * @return string
     */
    public function render(MenuContainer $menu);
}
