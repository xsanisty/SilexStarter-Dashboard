<?php

namespace Xsanisty\Admin\Helper;

use SilexStarter\Contracts\MenuRendererInterface;
use SilexStarter\Menu\MenuContainer;
use SilexStarter\Asset\AssetManager;
use Cartalyst\Sentry\Users\Eloquent\User;

class LteSidebarMenuRenderer implements MenuRendererInterface
{
    protected $assetManager;
    protected $options;
    protected $currentUser;

    public function __construct(AssetManager $assetManager, User $currentUser = null, array $options = [])
    {
        $this->assetManager = $assetManager;
        $this->options      = $options;
        $this->currentUser  = $currentUser;
    }

    public function render(MenuContainer $menu)
    {
        return $this->generateHtml($menu);
    }

    protected function generateHtml(MenuContainer $menu)
    {
        $html   = ($menu->getLevel() == 0)
                ? '<ul class="sidebar-menu">'
                : '<ul class="treeview-menu '.($menu->hasActiveItem() ? 'active' : '').'">';

        foreach ($menu->getItems() as $item) {

            if ($item->permission
                && $this->currentUser
                && !$this->currentUser->hasAnyAccess(
                    array_merge(['admin'], (array) $item->permission)
                )
            ) {
                continue;
            }

            if ($item->hasChildren()) {
                $format = '<li class="treeview %s">
                    <a href="#" title="%s" id="%s">
                        %s
                        <span>%s</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    %s
                </li>';
                $html .= sprintf(
                    $format,
                    $item->isActive() || $item->hasActiveChildren() ? 'active' : '',
                    $item->getAttribute('title'),
                    $item->getAttribute('name'),
                    $item->getAttribute('icon') ? '<i class="fa fa-fw fa-'.$item->getAttribute('icon').'"></i>' : '',
                    $item->getAttribute('label'),
                    $this->generateHtml($item->getChildContainer())
                );
            } else {
                $format =
                '<li class="treeview %s" id="%s">
                    <a href="%s" title="%s">%s  <span>%s</span></a>
                </li>';
                $html .= sprintf(
                    $format,
                    $item->getAttribute('class'). ($item->isActive() ? 'active' : ''),
                    $item->getAttribute('id'),
                    $item->getAttribute('url'),
                    $item->getAttribute('title'),
                    $item->getAttribute('icon') ? '<i class="menu-icon fa fa-fw fa-'.$item->getAttribute('icon').'"></i>' : '',
                    $item->getAttribute('label')
                );
            }
        }
        $html .= '</ul>';
        return $html;
    }
}
