<?php

namespace Xsanisty\Admin\Helper;

use SilexStarter\Admin\Contracts\MenuRendererInterface;
use SilexStarter\Admin\Menu\MenuContainer;
use SilexStarter\Asset\AssetManager;
use Cartalyst\Sentry\Users\UserInterface;

class LteBreadcrumbMenuRenderer implements MenuRendererInterface
{
    protected $currentUser;
    protected $assetManager;

    public function __construct(AssetManager $assetManager, UserInterface $user)
    {
        $this->currentUser  = $user;
        $this->assetManager = $assetManager;
    }

    public function render(MenuContainer $menu)
    {
        $format = '<li class="%s"><a href="%s" id="%s">%s  %s</a></li>';
        $html   = '<ol class="breadcrumb">';
        $items  = $menu->getItems();
        $last   = count($items);
        $index  = 1;

        foreach ($items as $item) {

            if ($item->permission
                && $this->currentUser
                && !$this->currentUser->hasAnyAccess(
                    array_merge(['admin'], (array) $item->permission)
                )
            ) {
                continue;
            }

            $html .= sprintf(
                $format,
                $item->getAttribute('class'),
                $index == $last ? 'javascript:void(0)' : $item->getAttribute('url'),
                $item->getAttribute('name'),
                $item->getAttribute('icon') ? '<i class="menu-icon fa fa-fw fa-'.$item->getAttribute('icon').'"></i>' : '',
                $item->getAttribute('label')
            );

            $index++;
        }

        $html .= '</ol>';

        return $html;
    }
}
