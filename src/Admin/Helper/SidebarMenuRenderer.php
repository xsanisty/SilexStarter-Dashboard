<?php

namespace Xsanisty\Admin\Helper;

use SilexStarter\Menu\MenuRendererInterface;
use SilexStarter\Menu\MenuContainer;

class SidebarMenuRenderer implements MenuRendererInterface
{
    protected $menu;

    public function render(MenuContainer $menu)
    {
        return $this->createHtml($menu);
    }

    protected function createHtml(MenuContainer $menu)
    {
        $format = '<li class="%s" id="%s"><a href="%s">%s  %s</a> %s </li>';
        $html   = ($menu->getLevel() == 0) ?
                '<ul class="sidebar"><li class="sidebar-main" id="toggle">
                  <a href="#">
                    Dashboard
                    <span class="menu-icon glyphicon glyphicon-transfer"></span>
                  </a>
                </li>' : '';

        foreach ($menu->getItems() as $item) {
            if ($item->hasChildren()) {
                $html .= '<li class="sidebar-title"><span>'.$item->getAttribute('label').'</span></li>';
                $html .= $this->createHtml($item->getChildContainer());
            } else {
                $html .= sprintf(
                    $format,
                    $item->getAttribute('class'). ' sidebar-list',
                    $item->getAttribute('id'),
                    $item->getAttribute('url'),
                    $item->getAttribute('label'),
                    ($item->getAttribute('icon')) ? '<span class="menu-icon glyphicon glyphicon-'.$item->getAttribute('icon').'"></span>' : '',
                    $this->createHtml($item->getChildContainer())
                );
            }
        }
        $html .= ($menu->getLevel() == 0) ? '</ul>' : '';

        return $html;
    }
}
